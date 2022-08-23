<?php
namespace App\Services;

use App\Models\User;
use App\Models\Client;
// use App\Models\Script;
use Illuminate\Support\Facades\DB;
use App\Contracts\ModelViewConnector;
use App\Helpers\AppHelper;
use Illuminate\Contracts\Database\Eloquent\Builder;

// use Hamcrest\Arrays\IsArray;
// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Collection;
// use Illuminate\Contracts\Database\Query\Builder;
// use Symfony\Component\Finder\Exception\AccessDeniedException;

class ClientService implements ModelViewConnector
{
    use IsModelViewConnector;

    public function __construct()
    {
        $this->selects = [
            'c.id as id',
            'c.rm_id as rm_id',
            'c.name as name',
            'c.client_code as client_code',
            'u.name as dealer',
            'c.total_aum as aum',
            'c.category as category',
            'c.realised_pnl as realised_pnl',
            'cst.cur_value as cur_value',
            'cst.tracked as tracked',
            DB::raw('(cst.cur_value - cst.allocated_aum) as pnl'),
            DB::raw('(cst.cur_value - cst.allocated_aum) / cst.allocated_aum * 100 as pnl_pc'),
            'cst.allocated_aum as allocated_aum',
            DB::raw('cst.allocated_aum / c.total_aum * 100 as pa'),
            'lbq.liquidbees as liquidbees',
            DB::raw('c.total_aum - cst.allocated_aum as cash'),
            DB::raw('(c.total_aum - cst.allocated_aum) / c.total_aum * 100 as cash_pc'),
            'c.ledger_balance as ledger_balance',
            DB::raw('c.realised_pnl + cst.cur_value - c.total_aum as returns'),
            DB::raw('(c.realised_pnl + cst.cur_value - c.total_aum) /c.total_aum * 100 as returns_pc'),
        ];

        $this->agrSelects = array_merge($this->selects, [
            DB::raw('SUM(c.total_aum) as agr_aum'),
            DB::raw('SUM(c.ledger_balance) as agr_ledger_balance'),
            DB::raw('SUM(cst.allocated_aum) as agr_allocated_aum'),
            DB::raw('SUM(cst.allocated_aum) / SUM(c.total_aum) * 100 as agr_pa'),
            DB::raw('SUM(c.realised_pnl) as agr_realised_pnl'),
            DB::raw('SUM(cst.cur_value) as agr_cur_value'),
            DB::raw('(SUM(cst.cur_value) - SUM(cst.allocated_aum)) as agr_pnl'),
            DB::raw('(SUM(cst.cur_value) - SUM(cst.allocated_aum)) / SUM(cst.allocated_aum) * 100 as agr_pnl_pc'),
            DB::raw('SUM(lbq.liquidbees) as agr_liquidbees'),
            DB::raw('SUM(c.total_aum) - SUM(cst.allocated_aum) as agr_cash'),
            DB::raw('(SUM(c.total_aum) - SUM(cst.allocated_aum)) / SUM(c.total_aum) as agr_cash_pc'),
            DB::raw('SUM(c.realised_pnl + cst.cur_value - c.total_aum) as agr_returns'),
            DB::raw('SUM(c.realised_pnl + cst.cur_value - c.total_aum) / SUM(c.total_aum) * 100 as agr_returns_pc'),
        ]);
        $this->searchesMap = [
            'dealer' => 'u.name',
            'name' => 'c.name',
            'aum' => 'c.total_aum',
            'cmp' => 's.cmp',
            'dp_qty' => 'cs.dp_qty',
            'buy_avg_price' => 'cs.buy_avg_price',
            'realised_pnl' => 'c.realised_pnl',
            'cur_value' => 'cst.cur_value',
            'allocated_aum' => 'cst.allocated_aum',
            'liquidbees' => 'lbq.liquidbees',
        ];

        /******* Item query *******/

        $this->itemQuery = Client::query();
        $this->relationSelects = [
            's.id as id',
            'c.total_aum as aum',
            'c.client_code as client_code',
            'cs.entry_date as entry_date',
            's.symbol as symbol',
            's.nse_code as nse_code',
            's.industry as industry',
            's.tracked as tracked',
            's.agio_indutry as sector',
            'cs.dp_qty as qty',
            'cs.buy_avg_price as buy_avg_price',
            DB::raw('cs.buy_avg_price * cs.dp_qty as amt_invested'),
            's.cmp as cmp',
            DB::raw('s.cmp * cs.dp_qty as cur_value'),
            DB::raw('(s.cmp - cs.buy_avg_price) * cs.dp_qty as overall_gain'),
            DB::raw('(s.cmp - cs.buy_avg_price) / cs.buy_avg_price * 100 as pc_change'),
            's.last_day_closing as ldc',
            DB::raw('(s.cmp - s.last_day_closing) * cs.dp_qty as todays_gain'),
            DB::raw('(s.cmp - s.last_day_closing) / s.last_day_closing * 100 as todays_gain_pc'),
            's.day_high as day_high',
            's.day_low as day_low',
            DB::raw('((s.cmp - cs.buy_avg_price) * cs.dp_qty) / c.total_aum * 100 as impact'),
            DB::raw('DATEDIFF(CURDATE(), cs.entry_date) as nof_days'),
            DB::raw('cs.buy_avg_price * cs.dp_qty * 100 / c.total_aum as pa'),
        ];

        $this->relAgrSelects = array_merge($this->relationSelects, [
            DB::raw('SUM(cs.buy_avg_price * cs.dp_qty) / SUM(c.total_aum) * 100 as agr_pa'),
            DB::raw('SUM(cs.buy_avg_price * cs.dp_qty) as agr_amt_invested'),
            DB::raw('SUM(s.cmp * cs.dp_qty) as agr_cur_value'),
            DB::raw('SUM((s.cmp - cs.buy_avg_price) * cs.dp_qty) as agr_overall_gain'),
            DB::raw('SUM((s.cmp - cs.buy_avg_price) * cs.dp_qty) / SUM(cs.buy_avg_price * cs.dp_qty) * 100 as agr_pc_change'),
            DB::raw('SUM((s.cmp - s.last_day_closing) * cs.dp_qty) as agr_todays_gain'),
            DB::raw('SUM((s.cmp - s.last_day_closing) * cs.dp_qty) / SUM(s.last_day_closing * cs.dp_qty) * 100 as agr_todays_gain_pc'),
        ]);

        $this->relSearchesMap = [
            'aum' => 'c.total_aum',
            'entry_date' => 'cs.entry_date',
            'symbol' => 's.symbol',
            'industry' => 's.industry',
            'sector' => 's.agio_indutry',
            'cmp' => 's.cmp',
            'dp_qty' => 'cs.dp_qty',
            'buy_avg_price', 'cs.buy_avg_price',
            'day_high' => 's.day_high',
            'day_low' => 's.day_low'
        ];

        $this->sortsMap = [
            'dealer' => ['name' => 'u.name', 'type' => 'string'],
            'name' => ['name' => 'c.name', 'type' => 'string'],
            'aum' => ['name' => 'c.total_aum', 'type' => 'float'],
            'cmp' => ['name' => 's.cmp', 'type' => 'float'],
            'dp_qty' => ['name' => 'cs.dp_qty', 'type' => 'integer'],
            'buy_avg_price' => ['name' => 'cs.buy_avg_price', 'type' => 'float'],
            'realised_pnl' => ['name' => 'c.realised_pnl', 'type' => 'float'],
            'cur_value' => ['name' => 'cst.cur_value', 'type' => 'float'],
            'allocated_aum' => ['name' => 'cst.allocated_aum', 'type' => 'float'],
            'liquidbees' => ['name' => 'lbq.liquidbees', 'type' => 'float'],
            'ledger_balance' => ['name' => 'c.ledger_balance', 'type' => 'float'],
        ];

        $this->relSortsMap = [
            'aum' => ['name' => 'c.total_aum', 'type' => 'float'],
            'entry_date' => ['name' => 'cs.entry_date', 'type' => 'string'],
            'symbol' => ['name' => 's.symbol', 'type' => 'string'],
            'industry' => ['name' => 's.industry', 'type' => 'string'],
            'sector' => ['name' => 's.agio_indutry', 'type' => 'string'],
            'cmp' => ['name' => 's.cmp', 'type' => 'float'],
            'dp_qty' => ['name' => 'cs.dp_qty', 'type' => 'integer'],
            'buy_avg_price', ['name' => 'cs.buy_avg_price', 'type' => 'float'],
            'day_high' => ['name' => 's.day_high', 'type' => 'float'],
            'day_low' => ['name' => 's.day_low', 'type' => 'float'],
            'overall_gain' => ['name' => '(s.cmp - cs.buy_avg_price) * cs.dp_qty', 'type' => 'float']
        ];
        $this->relFiltersMap = [
            'tracked' => ['name' => 's.tracked', 'type' => 'boolean']
        ];
        $this->relAdvSearchesMap = array_merge(
            $this->relSearchesMap,
            [
                'pc_change' => '(s.cmp - cs.buy_avg_price) / cs.buy_avg_price * 100',
                'pa' => 'cs.buy_avg_price * cs.dp_qty * 100 / c.total_aum',
                'sector' => 's.agio_indutry',
                'nof_days' => 'DATEDIFF(CURDATE(), cs.entry_date)',
            ]
        );

        $this->idKey = 'id';
        $this->selIdsKey = 'c.id';
        $this->relSelIdsKey = 's.id';
        $this->uniqueSortKey = 'c.id';
        $this->relUniqueSortKey = 's.id';
    }

    private function getQuery(): Builder
    {
        /******* Index query *******/

        $indexScriptQ = DB::table('clients_scripts as cs')
            ->join('scripts as s', 's.id', '=', 'cs.script_id')
            ->select(
                'cs.client_id as cid',
                's.id as sid',
                's.cmp as cmp',
                's.tracked as tracked',
                'cs.buy_avg_price as buy_avg_price',
                'cs.dp_qty as dp_qty',
                DB::raw(DB::raw('SUM(cs.dp_qty * cs.buy_avg_price)').' as allocated_aum'),
                DB::raw(DB::raw('SUM(cs.dp_qty * s.cmp)').' as cur_value')
            )->where('s.tracked', 1)->groupBy('cs.client_id')
            ->where('cs.dp_qty', '>', 0);

        $lbs = AppHelper::getLiquidbees();

        $indexLBQ = DB::table('clients_scripts as lbcs')
            ->select(
                'lbcs.client_id as lbcs_client_id',
                'lbcs.script_id as lbcs_script_id',
                DB::raw(DB::raw('SUM(lbcs.dp_qty * lbcs.buy_avg_price)').' as liquidbees')
            )
            ->whereIn('script_id', $lbs)
            ->groupBy('lbcs.client_id');

        $query = Client::from('clients as c')->userAccessControlled()
            ->joinSub($indexScriptQ, 'cst', 'c.id' , '=', 'cst.cid', 'left')
            ->joinSub($indexLBQ, 'lbq', 'c.id' , '=', 'lbq.lbcs_client_id', 'left')
            ->join('users as u', 'c.rm_id', '=', 'u.id');

        return $query;
    }

    protected function applyGroupings($query) {
        return $query->groupBy('id');
    }

    protected function getRelationQuery(int $id = null)
    {
        return Client::from('clients as c')
            ->join('clients_scripts as cs', 'c.id' , '=', 'cs.client_id')
            ->join('scripts as s', 's.id', '=', 'cs.script_id')
            ->where('c.id', $id)
            ->where('cs.dp_qty', '>', 0);
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
        // dd($results);
        $formatted = [];
        foreach ($results as $result) {
            $aum = $result['total_aum'] ?? 0;
            $cv = $result['cur_value'];
            $al_aum = $result['allocated_aum'];

            $row = $result;
            $row['total_aum'] = round($aum, 2);
            $row['allocated_aum'] = round($al_aum, 2) ?? 0;
            $row['cur_value'] = round($cv, 2) ?? 0;
            $row['realised_pnl'] = round($result['realised_pnl'], 2);
            $row['pnl'] = round($result['pnl'], 2);
            $row['pnl_pc'] = round($result['pnl_pc'], 2);
            $row['pa'] = round($result['pa'], 2);
            $row['liquidbees'] = isset($result['liquidbees']) ? round($result['liquidbees'], 2) : 0;
            $row['cash'] = round($result['cash'], 2);
            $row['cash_pc'] = round($result['cash_pc'], 2);
            $row['returns'] = round($result['returns'], 2);
            $row['returns_pc'] = round($result['returns_pc'], 2);

            $formatted[] = $row;
        }
        // dd($formatted);
        return $formatted;
    }

    public function formatRelationResults(mixed $results): array
    {
        $formatted = [];
        foreach ($results as $result) {
            $row = [];

            if (is_array($result)) {
                $row = $result;
            } else {
                $cmp = $result->cur_value;
                $qty = $result->qty;
                $amt_invested = $result->amt_invested;
                $ldc = $result->ldc;
                $aum = $result->aum;
                $sid = $result->id;

                $row['id'] = $sid;
                $row['aum'] = round($aum, 2);
                $row['entry_date'] = $result->entry_date;
                $row['symbol'] = $result->symbol;
                $row['industry'] = $result->industry;
                $row['sector'] = $result->sector;
                $row['qty'] = $result->qty;
                $row['buy_avg_price'] = round($result->buy_avg_price, 2);
                $row['amt_invested'] = round($result['amt_invested'], 2);
                $row['cmp'] = round($result->cmp, 2);
                $row['cur_value'] = round($result->cur_value, 2);
                $row['ldc'] = round($result->ldc, 2);
                $row['day_high'] = round($result->day_high, 2);
                $row['day_low'] = round($result->day_low, 2);
                $row['nof_days'] = $result->nof_days;
                $row['pa'] = round($result->pa, 2);
                $row['overall_gain'] = round($result['overall_gain'], 2);
                $row['pc_change'] = round($result['pc_change'], 2);
                $row['todays_gain'] = round($result['todays_gain'], 2);
                $row['impact'] = round($result['impact'], 2);
            }


            // $row['cur_value'] = $cmp * $qty;
            // $row['overall_gain'] = $row['cur_value'] - $amt_invested;
            // $row['pc_change'] = $amt_invested > 0 ? $row['overall_gain'] / $amt_invested * 100 : 0;
            // $row['todays_gain'] = $row['cur_value'] - $qty * $ldc;
            // $row['impact'] =  $aum > 0 ? $row['overall_gain'] / $aum * 100 : 0;

            $formatted[] = $row;
        }
        return $formatted;
    }

    public function downloadOrder(
        $id,
        array $searches,
        array $sorts,
        array $filters,
        array $advSearch,
        string | null $selectedIds,
        int $qty,
        string | float $price,
        float $slippage
    ) {
        $queryData = $this->getRelationQueryAndParams(
            $this->getRelationQuery($id),
            $searches,
            $sorts,
            $filters,
            $advSearch,
            $selectedIds,
        );

        DB::statement("SET SQL_MODE=''");
        $results = $queryData['query']->select($this->relationSelects)->get();
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
            $p = round($result->cmp * (100 - $slippage) / 100, 2);
            $p = round($p * 20) / 20; // round(($p * 100) / 5) * 5 / 100
            $row['script_name'] = $result->symbol.' EQ| '.$result->nse_code;
            $row['qty'] = round(($result->qty * $qty /100)) . '';
            $row['lot'] = round(($result->qty * $qty /100)) . '';
            $row['price'] = $p.'';
            $row['client_code'] = $result->client_code;
            $export[] = $row;
        }
        return $export;
    }

    // private function getFilterParams($query, $filters) {
    //     return [];
    // }

    // public function bulkImport($file)
    // {
    //     return true;
    // }

    public function update($id, $data)
    {
        return Client::where('id', $id)->update($data);
    }

    public function updateScript($id, $data)
    {
        $client = Client::find($id);
        return $client->scripts()->updateExistingPivot($data['script_id'], [
                'dp_qty' => $data['qty'],
                'buy_avg_price' => $data['buy_avg_price']
            ]);
    }
}

?>