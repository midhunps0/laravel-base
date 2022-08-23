<?php

namespace App\ImportExports;

use App\Models\User;
use App\Models\Client;
use App\Models\Script;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClientsImport implements ToCollection, WithHeadingRow
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

            $c = Client::where('client_code', $item['client_code'])->count();
            if ($c > 0) {
                $this->failedItems[] = [
                    'client_code' => $item['client_code'],
                    'name' => $item['name'],
                    'status' => 'Duplicate client code.'
                ];
                continue;
            }

            $dealer = User::where('username', $item['rm'])->get()->first();

            $client = new Client();

            if ($dealer == null) {
                $this->failedItems[] = [
                    'client_code' => $item['client_code'],
                    'name' => $item['name'],
                    'status' => 'Invalid dealer username.'
                ];
                continue;
            }

            $client->rm_id = $dealer->id;
            $attrs = [
                'client_code' => $item['client_code'],
                'unique_code' => $item['unique_code'],
                'name' => $item['name'],
                'fresh_fund' => $item['fresh_fund'],
                're_invest' => $item['re_invest'],
                'withdrawal' => $item['withdrawal'],
                'payout' => $item['payout'],
                'total_aum' => $item['total_aum'],
                'other_funds' => $item['other_funds'],
                'brokerage' => $item['brokerage'],
                'realised_pnl' => $item['realised_pnl'],
                'ledger_balance' => $item['ledger_balance'],
                'pfo_type' => $item['pfo_type'],
                'category' => $item['category'],
                'type' => $item['type'],
                'fno' => $item['fno'],
                'pan_number' => $item['pan_number'],
                'email' => $item['email'],
                'phone_number' => $item['phone_number'],
                'whatsapp' => $item['whatsapp']
            ];
            $this->setAttributes($client, $attrs);
            $client->save();

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
