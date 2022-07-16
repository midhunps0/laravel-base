<?php
namespace App\Services;

use App\Models\User;
use App\Models\Client;
// use App\Models\Script;
use App\Helpers\AppHelper;
use Illuminate\Support\Facades\DB;
use App\Contracts\ModelViewConnector;
use Illuminate\Contracts\Database\Query\Builder;

// use Hamcrest\Arrays\IsArray;
// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Collection;
// use Illuminate\Contracts\Database\Query\Builder;
// use Symfony\Component\Finder\Exception\AccessDeniedException;

class ClientScriptService implements ModelViewConnector
{
    use IsModelViewConnector;

    protected $onlyTracked = true;

    public function __construct()
    {
        $indexScriptQ = DB::table('clients_scripts as cs')
            ->join('scripts as s', 's.id', '=', 'cs.script_id')
            ->select(
                'cs.client_id as cid',
                's.id as script_id',
                's.cmp as cmp',
                'cs.buy_avg_price as buy_avg_price',
                'cs.dp_qty as dp_qty',
                'cs.entry_date as entry_date',
                DB::raw(DB::raw('SUM(cs.dp_qty * cs.buy_avg_price)').' as allocated_aum'),
                DB::raw(DB::raw('SUM(cs.dp_qty * s.cmp)').' as cur_value')
            )->groupBy('cs.client_id');

        $lbs = AppHelper::getLiquidbees();

        $indexLBQ = DB::table('clients_scripts as lbcs')
            ->select(
                'lbcs.client_id as lbcs_client_id',
                'lbcs.script_id as lbcs_script_id',
                DB::raw(DB::raw('SUM(lbcs.dp_qty * lbcs.buy_avg_price)').' as liquidbees')
            )
            ->whereIn('script_id', $lbs)
            ->groupBy('lbcs.client_id');

        $this->query = Client::from('clients as c')->userAccessControlled()
            ->join('clients_scripts as clsc', 'c.id', '=', 'clsc.client_id', 'inner')
            ->join('scripts as s', 's.id', '=', 'clsc.script_id', 'inner')
            ->joinSub($indexScriptQ, 'cst', 'c.id' , '=', 'cst.cid', 'left')
            ->joinSub($indexLBQ, 'lbq', 'c.id' , '=', 'lbq.lbcs_client_id', 'left')
            ->join('users as u', 'c.rm_id', '=', 'u.id', 'left');

        $this->selects = [
            'c.id as id',
            'c.rm_id as rm_id',
            'c.name as name',
            'c.client_code as client_code',
            'c.category as client_category',
            'u.name as dealer',
            'c.total_aum as aum',
            'c.realised_pnl as realised_pnl',
            'cst.cur_value as client_cur_value',
            's.id as sid',
            // DB::raw('IF (s.id IS NOT NULL, s.id, \'0\') as sid'),
            's.symbol as symbol',
            's.nse_code as nse_code',
            's.tracked as tracked',
            's.cmp as cmp',
            's.last_day_closing as ldc',
            's.day_high as day_high',
            's.day_low as day_low',
            's.industry as industry',
            's.mvg_sector as sector',
            'cst.dp_qty as qty',
            'cst.buy_avg_price as buy_avg_price',
            'cst.entry_date as dop',
            'cst.allocated_aum as allocated_aum',
            'lbq.liquidbees as liquidbees',
            DB::raw('cst.buy_avg_price * cst.dp_qty as buy_val'),
            DB::raw('cst.dp_qty * s.cmp as script_cur_value'),
            DB::raw('(s.cmp - cst.buy_avg_price) * cst.dp_qty as pnl'),
            DB::raw('(s.cmp - cst.buy_avg_price) / cst.buy_avg_price * 100 as pnl_pc'),
            DB::raw('cst.buy_avg_price * cst.dp_qty * 100 / c.total_aum as pa'),
            DB::raw('DATEDIFF(CURDATE(), cst.entry_date) as nof_days'),
            DB::raw('(s.cmp - cst.buy_avg_price) * cst.dp_qty / c.total_aum * 100 as impact'),
            DB::raw('c.total_aum - cst.allocated_aum as cash'),
            DB::raw('(c.total_aum - cst.allocated_aum) / c.total_aum * 100 as cash_pc'),
            DB::raw('c.realised_pnl + cst.cur_value - c.total_aum as returns'),
            DB::raw('(c.realised_pnl + cst.cur_value - c.total_aum) /c.total_aum * 100 as returns_pc'),
            DB::raw('cst.cur_value - c.total_aum as client_pnl'),
            DB::raw('(cst.cur_value - c.total_aum) / c.total_aum * 100 as client_pnl_pc'),
            DB::raw('IF(s.id IS NULL, CONCAT(c.id, "::", "0"), CONCAT(c.id, "::", s.id)) as uxid'),
        ];

        $this->searchesMap = [
            'id' => 'c.id',
            'rm_id' => 'c.rm_id',
            'name' => 'c.name',
            'client_code' => 'c.client_code',
            'client_category' => 'c.category',
            'dealer' => 'u.name',
            'aum' => 'c.total_aum',
            'realised_pnl' => 'c.realised_pnl',
            'client_cur_value' => 'cst.cur_value',
            'sid' => 's.id',
            'symbol' => 's.symbol',
            'nse_code' => 's.nse_code',
            'tracked' => 's.tracked',
            'cmp' => 's.cmp',
            'ldc' => 's.last_day_closing',
            'day_high' => 's.day_high',
            'day_low' => 's.day_low',
            'industry' => 's.industry',
            'sector' => 's.mvg_sector',
            'qty' => 'cst.dp_qty',
            'buy_avg_price' => 'cst.buy_avg_price',
            'buy_avg_price' => 'cst.buy_avg_price',
            'allocated_aum' => 'cst.allocated_aum',
            'liquidbees' => 'lbq.liquidbees',
        ];
        $this->advSearchesMap = array_merge(
            $this->searchesMap,
            [
                'buy_val' => 'cst.buy_avg_price * cst.dp_qty',
                'script_cur_value' => 'cst.dp_qty * s.cmp',
                'pnl' => '(s.cmp - cst.buy_avg_price) * cst.dp_qty',
                'pnl_pc' => '(s.cmp - cst.buy_avg_price) / cst.buy_avg_price * 100',
                'pa' => 'cst.buy_avg_price * cst.dp_qty * 100 / c.total_aum',
                'nof_days' => 'DATEDIFF(CURDATE(), cst.entry_date)',
                'impact' => '(s.cmp - cst.buy_avg_price) * cst.dp_qty / c.total_aum * 100',
                'cash' => 'c.total_aum - cst.allocated_aum',
                'cash_pc' => '(c.total_aum - cst.allocated_aum) / c.total_aum * 100',
                'returns' => 'c.realised_pnl + cst.cur_value - c.total_aum',
                'returns_pc' => '(c.realised_pnl + cst.cur_value - c.total_aum) /c.total_aum * 100',
                'client_pnl' => 'cst.cur_value - c.total_aum',
                'client_pnl_pc' => '(cst.cur_value - c.total_aum) / c.total_aum * 100',
                'uxid' => 'CONCAT(c.id, "::", s.id',
            ]
        );
        $this->sortsMap = [

            'id' => ['name' => 'c.id', 'type' => 'integer'],
            'rm_id' => ['name' => 'c.rm_id', 'type' => 'integer'],
            'name' => ['name' => 'c.name', 'type' => 'string'],
            'client_code' => ['name' => 'c.client_code', 'type' => 'string'],
            'client_category' => ['name' => 'c.category', 'type' => 'string'],
            'dealer' => ['name' => 'u.name', 'type' => 'string'],
            'aum' => ['name' => 'c.total_aum', 'type' => 'float'],
            'realised_pnl' => ['name' => 'c.realised_pnl', 'type' => 'float'],
            'client_cur_value' => ['name' => 'cst.cur_value', 'type' => 'float'],
            'sid' => ['name' => 's.id', 'type' => 'integer'],
            'symbol' => ['name' => 's.symbol', 'type' => 'string'],
            'nse_code' => ['name' => 's.nse_code', 'type' => 'string'],
            'tracked' => ['name' => 's.tracked', 'type' => 'boolean'],
            'cmp' => ['name' => 's.cmp', 'type' => 'float'],
            'ldc' => ['name' => 's.last_day_closing', 'type' => 'float'],
            'day_high' => ['name' => 's.day_high', 'type' => 'float'],
            'day_low' => ['name' => 's.day_low', 'type' => 'string'],
            'industry' => ['name' => 's.industry', 'type' => 'string'],
            'sector' => ['name' => 's.mvg_sector', 'type' => 'string'],
            'qty' => ['name' => 'cst.dp_qty', 'type' => 'integer'],
            'buy_avg_price' => ['name' => 'buy_avg_price', 'type' => 'float'],
            'allocated_aum' => ['name' => 'allocated_aum', 'type' => 'float'],
            'liquidbees' => ['name' => 'lbq.liquidbees', 'type' => 'float'],
        ];

        $this->selIdsKey = 'uxid';
        $this->relSelIdsKey = 'c.id';
        $this->uniqueSortKey = 'uxid';
    }

    public function verifySellOrder(
        array $searches,
        array $sorts,
        array $filters,
        array $advSearch,
        string | null $selectedIds,)
    {
        $queryData = $this->getQueryAndParams(
            $searches,
            $sorts,
            $filters,
            $advSearch,
            $selectedIds
        );

        DB::statement("SET SQL_MODE=''");
        $results = $queryData['query']->select($this->selects)->get();
        DB::statement("SET SQL_MODE='only_full_group_by'");

        $valid = true;
        foreach ($results as $result) {
            if ($result->sid == null) {
                $valid = false;
                break;
            }
        }
        return $valid;
    }
    public function analyseSellOrder(
        array $searches,
        array $sorts,
        array $filters,
        array $advSearch,
        string | null $selectedIds,)
    {
        $queryData = $this->getQueryAndParams(
            $searches,
            $sorts,
            $filters,
            $advSearch,
            $selectedIds
        );

        DB::statement("SET SQL_MODE=''");
        $results = $queryData['query']->select($this->selects)->get();
        DB::statement("SET SQL_MODE='only_full_group_by'");

        $unique = true;
        $symbol = '';
        $price = 0.00;
        $first = $results->first();

        foreach ($results as $result) {
            if ($result->sid != $first->sid) {
                $unique = false;
                break;
            }
        }

        if ($unique) {
            $symbol = $first->symbol;
            $price = $first->cmp;
        }
        return [
            'unique' => $unique,
            'symbol' => $symbol,
            'price' => $price
        ];
    }

    protected function applyGroupings($query) {
        return $query->groupBy('id');
    }

    protected function extraConditions($query): void {
        if (!(isset($this->onlyTracked) && !$this->onlyTracked)) {
            $query->where(function ($query) {
                $query->where('s.tracked', '=', true)
                    ->orWhereNull('s.tracked');
                }
            );
        }
    }

    public function getIdsForParams(
        array $searches,
        array $sorts,
        array $filters,
        array $advSearch,
    ): array {
        $queryData = $this->getQueryAndParams(
            $searches,
            $sorts,
            $filters,
            $advSearch
        );

        DB::statement("SET SQL_MODE=''");
        $results = $queryData['query']->select($this->selects)->get()->pluck('uxid')->toArray();
        DB::statement("SET SQL_MODE='only_full_group_by'");

        // $ids = [];
        // foreach ($results as $result) {
        //     $ids[] = $result->id.'::'.$result->sid;
        // }

        return $results;
    }

    private function getItemIds($results) {
        $ids = $results->pluck('uxid')->toArray();
        return json_encode($ids);
    }

    protected function getRelationQuery(int $id = null)
    {
        return $this->query;
    }

    public function getList($search)
    {
        $clients = Client::where('client_code', 'like', $search.'%')
            ->orWhere('name', 'like', '%'.$search.'%')->select(['id', 'client_code as code', 'name'])->userAccessControlled()->limit(15)->get();
        return [
            'clients' => $clients
        ];
    }

    protected function accessCheck($item): bool
    {
        $client = $item;
        $user = auth()->user();
        if($user->hasRole('Dealer')) {
            if ($client->rm_id != $user->id) {
                return false;
            }
        } else if($user->hasRole('Team Leader')) {
            $dealers = array_values(User::where('teamleader_id', $user->id)->pluck('id')->toArray());
            $dealers[] = $user->id;
            if (!in_array($client->rm_id, $dealers)) {
                return false;
            }
        }
        return true;
    }

    public function formatIndexResults(mixed $results): array
    {
        $formatted = [];
        foreach ($results as $result) {
            $aum = $result['aum'] ?? 0;
            $cv = $result['client_cur_value'];
            $al_aum = $result['allocated_aum'];

            $row = $result;
            $row['aum'] = $aum;
            $row['allocated_aum'] = $al_aum ?? 0;
            $row['client_cur_value'] = $cv ?? 0;
            $row['client_pnl_pc'] = $aum > 0 ? $result['client_pnl_pc'] : 0;

            $formatted[] = $row;
        }
        return $formatted;
    }

    private function querySelectedIds(Builder $query, string $idKey, array $ids): void
    {
        $idStr = '|'.implode('|', $ids).'|';
        $query->where(DB::raw('INSTR("'.$idStr.'", CONCAT("|", c.id, "::", s.id, "|"))'), '>', 0);
        // DB::raw('IF(s.id IS NULL, CONCAT(c.id, "::", "0"), CONCAT(c.id, "::", s.id)) as uxid')
    }

    public function downloadOrder(
        array $searches,
        array $sorts,
        array $filters,
        array $advSearch,
        string | null $selectedIds,
        int $qty,
        string | float $price,
        float $slippage
    ) {
        $queryData = $this->getQueryAndParams(
            $searches,
            $sorts,
            $filters,
            $advSearch,
            $selectedIds,
        );

        DB::statement("SET SQL_MODE=''");
        $results = $queryData['query']->select($this->selects)->get();
        DB::statement("SET SQL_MODE='only_full_group_by'");

        $export = [];
        foreach ($results as $result) {
            $row = [
                'exchange_code' => 'NSE',
                'buyorsell' => 'S',
                'product' => 'SellFromDP',
                'order_type' => 'L',
                'discount_qty' => '0',
                'trigger_price' => '0'
            ];
            $xprice = $price == 'null' ? $result->cmp : $price;
            $row['script_name'] = $result->symbol.' EQ| '.$result->nse_code;
            $row['qty'] = $qty.'';
            $row['lot'] = $qty.'';
            $row['price'] = round($xprice * (100 - $slippage) / 100, 2) . '';
            $row['client_code'] = $result->client_code;
            $export[] = $row;
        }
        return $export;
    }

    // private function getSortParams($query, array $sorts, string $sortType = 'index'): array
    // {
    //     $map = $sortType == 'index' ? $this->sortsMap : $this->relSortssMap;
    //     $sortParams = [];
    //     foreach ($sorts as $sort) {
    //         $data = explode('::', $sort);
    //         $key = $map[$data[0]] ?? $data[0];
    //         // $query->orderBy($key, $data[1]);
    //         $query->orderByRaw('CONCAT('.$key.',\'::\',uxid) '.$data[1]);
    //         // $sortParams[$data[0]] = $data[1];
    //     }
    //     // dd($sortParams);
    //     return $sortParams;
    // }

    private function getFilterParams($query, $filters) {
        return [];
    }
}

?>