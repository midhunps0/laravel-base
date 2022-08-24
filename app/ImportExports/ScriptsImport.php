<?php

namespace App\ImportExports;

use App\Models\Script;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ScriptsImport implements ToCollection, WithHeadingRow
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
            info($item);
            if (isset($item['isin_code']) && isset($item['symbol'])) {
                $this->totalItems++;
            } else {
                continue;
            }
            $c = Script::where('isin_code', $item['isin_code'])
                ->orWhere('symbol', $item['symbol'])
                ->orWhere('company_name', $item['company_name'])
                ->count();
            if ($c > 0) {
                $this->failedItems[] = [
                    'symbol' => $item['symbol'],
                    'isin_code' => $item['isin_code'],
                    'company_name' => $item['company_name'],
                    'status' => 'Duplicate Script.'
                ];
                continue;
            }

            $script = new Script();
            $attrs = [
                'isin_code' => $item['isin_code'],
                'symbol' => $item['symbol'],
                'tracked' => $item['tracked'],
                'company_name' => $item['company_name'],
                'industry' => $item['industry'],
                'series' => $item['series'],
                'fno' => $item['fno'],
                'nifty' => $item['nifty'],
                'nse_code' => $item['nse_code'],
                'bse_code' => $item['bse_code'],
                'yahoo_code' => $item['yahoo_code'],
                'doc' => $item['doc'],
                'bbg_ticker' => $item['bbg_ticker'],
                'bse_security_id' => $item['bse_security_id'],
                'capitaline_code' => $item['capitaline_code'],
                'mvg_sector' => $item['mvg_sector'],
                'agio_indutry' => $item['agio_industry'],
                'remarks' => $item['remarks'],
            ];
            $this->setAttributes($script, $attrs);
            $script->save();

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

    private function setAttributes($script, $attrVals)
    {
        foreach ($attrVals as $attr => $val) {
            if (isset($val)) {
                $script->$attr = $val;
            }
        }
    }
}
