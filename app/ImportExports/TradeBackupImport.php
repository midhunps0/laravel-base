<?php

namespace App\ImportExports;

use App\Models\Client;
use App\Models\Script;
use App\Models\TradeBackupItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TradeBackupImport implements ToCollection, WithHeadingRow
{
    use Importable;

    public $failedItems = [];
    public $totalItems = 0;
    /**
     * @param Collection $collection
     */
    public function collection(Collection $data)
    {
        // get Client Code, Symbol -> get client, get symbol -> sript id
        // Buy/Sell: B or S
        // Trade Qty, Trade Price -> amt = qty * price; old_dpq =  cs dp_qty;
        // B: cs dp_qty += qty; cs avg_buy_price = ((avg_buy_price * old_dpq) + amt) / dp_qty
        // S: cs dp_qty -= qty;
        // pnl = amt - client->script->buy_avg_amt * qty
        // realised_pnl += pnl;

        // $post->tags()->updateExistingPivot($tagId, [
        //     'name' => 'Give me a name',
        // ]);
        $success_items = [];
        foreach ($data as $item) {
            if (isset($item['client_code'])) {
                $this->totalItems++;
            } else {
                continue;
            }
            info('----Start----');
            info('Client: ' . $item['client_code']);
            $success = true;
            $itemStatus = ['client' => 'Ok', 'script' => 'Unchecked', 'client_script' => 'Unchecked', 'trade_date' => 'Ok'];
            $client = Client::with('scripts')->where('client_code', $item['client_code'])->get()->first();
            $script = Script::where('symbol', $item['symbol'])->first();
            $itemStatus['client_code'] = $item['client_code'];
            $itemStatus['script_symbol'] = $item['symbol'];
            if ($client == null) {
                $success = false;
                $itemStatus['client'] = 'Not Found';
            }
            if ($script != null) {
                $scriptId = $script->id;
            } else {
                $success = false;
                $itemStatus['script'] = 'Not Found';
            }
            $thedate = explode(' ', $item['trade_date_time'])[0];
            if (strpos($thedate, '-') > 0) {
                $d = explode('-', $thedate);
            } else if (strpos($thedate, '/') > 0) {
                $d = explode('/', $thedate);
            } else {
                $itemStatus['trade_date'] = 'Invalid date';
            }
            $ddate = $d[2] . '-' . $d[1] . '-' . $d[0];

            if ($success) {
                $tbi = TradeBackupItem::where('date', $ddate)
                    ->where('trade_no', 'like', $item['trade_no'])
                    // ->where('script_id', $scriptId)
                    // ->where('client_id', $client->id)
                    ->get()->first();
                if ($tbi != null) {
                    $success = false;
                    $itemStatus['trade_date'] = 'Duplicate backup: trade_no: '.$item['trade_no'].', date: '.$thedate;
                }
            }
            $qty = intval($item['trade_qty']);
            $price = floatval($item['trade_price']);
            $amt = $price * $qty;
            if ($success) {
                $theScript = $client->scripts->find($scriptId);
                info('qty: ' . $qty . 'price: ' . $price . 'amt: ' . $amt);
                if ($theScript != null) {
                    info('Script:');
                    info($theScript);
                    $oldQty = $theScript->pivot->dp_qty;
                    info('b a price: ' . $theScript->pivot->buy_avg_price);
                    $oldAvgBuyPrice = $theScript->pivot->buy_avg_price;
                    info('oldqty = ' . $oldQty);
                    info('oldavgprice = ' . $oldAvgBuyPrice);
                } else if ($item['buysell'] == 'B') {
                    $oldQty = 0;
                    $oldAvgBuyPrice = 0;
                } else {
                    $success = false;
                    $itemStatus['client_script'] = 'Not Found';
                    // continue;
                }
            }
            if (!$success) {
                $this->failedItems[] = $itemStatus;
                info('Skipping: ');
                info($itemStatus);
                continue;
            } else {
                // $newQty = $oldQty;
                // $newAvgPrice = $oldAvgBuyPrice;
                $oldRealisedPnl = $client->realised_pnl;
                $newRealisedPnl = $oldRealisedPnl;
                switch ($item['buysell']) {
                    case 'B':
                        info('Case B');
                        $newQty = $oldQty + $qty;
                        info('new qty: ' . $newQty);
                        $newAvgPrice = (($oldAvgBuyPrice * $oldQty) + $amt) / $newQty;
                        info('new avg price: ' . $newAvgPrice);

                        if (isset($theScript)) {
                            $client->scripts()->updateExistingPivot(
                                $scriptId,
                                [
                                    'dp_qty' => $newQty,
                                    'buy_avg_price' => $newAvgPrice
                                ]
                            );
                        } else {
                            $client->scripts()->attach(
                                $scriptId,
                                [
                                    'dp_qty' => $newQty,
                                    'available_qty' => $newQty,
                                    'entry_date' => date('Y-m-d'),
                                    'buy_avg_price' => $newAvgPrice
                                ]
                            );
                        }
                        break;
                    case 'S':
                        info('Case S');
                        $newQty = $oldQty - $qty;
                        info('new qty: ' . $newQty);
                        if ($newQty < 0) {
                            $newQty = 0;
                            info('new qty corrected: ' . $newQty);
                        }
                        $pnl = $amt - ($oldAvgBuyPrice * $qty);
                        info('pnl: ' . $pnl);

                        info('old realisedPnl: ' . $oldRealisedPnl);
                        $newRealisedPnl = $oldRealisedPnl + $pnl;
                        info('new realisedPnl: ' . $newRealisedPnl);

                        $client->scripts()->updateExistingPivot(
                            $scriptId,
                            [
                                'dp_qty' => $newQty
                            ]
                        );
                        $client->update(['realised_pnl' => $newRealisedPnl]);
                        break;
                }
                info('Trade No: ' . $item['trade_no']);

                TradeBackupItem::create([
                    'date' => $ddate,
                    'client_id' => $client->id,
                    'script_id' => $scriptId,
                    'trade_no' => $item['trade_no']
                ]);
                $success_items[] = $item['trade_no'];
                info('----Finish----');
            }
        }
        info('Success Items: ');
        info($success_items);
        return [$this->totalItems, $this->failedItems];
    }

    public function getFailedItems()
    {
        return $this->failedItems;
    }

    public function getTotalCount()
    {
        return $this->totalItems;
    }
}
