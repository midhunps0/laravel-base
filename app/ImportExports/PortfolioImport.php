<?php

namespace App\ImportExports;

use App\Models\User;
use App\Models\Client;
use App\Models\Script;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PortfolioImport implements ToCollection, WithHeadingRow
{
    use Importable;

    public $failedItems = [];
    public $totalItems = 0;
    /**
    * @param Collection $collection
    */
    public function collection(Collection $data)
    {
        foreach ($data as $item) {
            if (isset($item['client_code'])) {
                $this->totalItems++;
            } else {
                continue;
            }
            $script = Script::where('symbol', $item['symbol'])->get()->first();
            if ($script != null) {
                $script_id = $script->id;
            } else {
                $this->failedItems[] = [
                    'client_code' => $item['client_code'],
                    'symbol' => $item['symbol'],
                    'status' => 'Invalid symbol'
                ];
                continue;
            }

            $client = Client::where('client_code', $item['client_code'])->get()->first();
            if ($client == null) {
                $this->failedItems[] = [
                    'client_code' => $item['client_code'],
                    'symbol' => $item['symbol'],
                    'status' => 'Invalid client code'
                ];
                continue;
            }

            $scount = $client->scripts()->where('id', $script_id)->count();

            info('scount: '.$scount);
            if ($scount > 0) {
                $this->failedItems[] = [
                    'client_code' => $item['client_code'],
                    'symbol' => $item['symbol'],
                    'status' => 'Script already present in client portfolio.'
                ];
                continue;
            }
            info('date: '.$item['entry_date']);
            if (strpos($item['entry_date'], '-') > 0) {
                $d = explode('-', $item['entry_date']);
            } else if (strpos($item['entry_date'], '/') > 0) {
                $d = explode('/', $item['entry_date']);
            } else {
                $this->failedItems[] = [
                    'client_code' => $item['client_code'],
                    'symbol' => $item['symbol'],
                    'status' => 'Invalid date'
                ];
                continue;
            }


            $client->scripts()->attach($script_id, [
                'dp_qty' => $item['qty'],
                'buy_avg_price' => $item['buy_avg_price'],
                'entry_date' => $d[2].'-'.$d[1].'-'.$d[0],
            ]);
        }
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

    private function setAttributes($client, $attrVals)
    {
        foreach ($attrVals as $attr => $val) {
            if (isset($val)) {
                $client->$attr = $val;
            }
        }
    }
}
