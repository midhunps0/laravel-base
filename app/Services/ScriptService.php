<?php
namespace App\Services;

use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use App\Contracts\ModelViewConnector;
use App\Models\Script;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Database\Query\Builder;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class ScriptService implements ModelViewConnector
{
    use IsModelViewConnector;

    private $pa;
    private $tot_aum;

    public function __construct()
    {
        // $sum = Client::from('clients', 'c')->userAccessControlled()
        //     ->join('clients_scripts as cs', 'c.id', '=', 'cs.client_id')
        //     ->join('scripts as s', 's.id', '=', 'cs.script_id')
        //     ->where('s.tracked', 1)
        //     ->select(
        //     DB::raw('SUM(buy_avg_price * cs.dp_qty) as amt_invested'),
        // )->get()->first();
        $sum = Client::from('clients', 'c')->userAccessControlled()
            ->select(
                DB::raw('SUM(c.total_aum) as total_aum')
            )->get()->first();
        $this->tot_aum = $sum->total_aum;

        $this->selects = [
            'u.name as dealer',
            'u.id as dealer_id',
            'qcs.dop as dop',
            's.bse_code as bse_code',
            's.symbol as symbol',
            's.id as sid',
            DB::raw('qcs.amt_invested / '.$this->tot_aum.' * 100 as pa'),
            's.agio_indutry as sector',
            'qcs.qty as tot_qty',
            'qcs.category as category',
            DB::raw('ROUND(qcs.abv, 2) as abv'),
            DB::raw('ROUND(qcs.amt_invested, 2) as amt_invested'),
            's.cmp as cmp',
            DB::raw('ROUND(s.cmp * qcs.qty, 2) as cur_value'),
            DB::raw('ROUND((s.cmp - qcs.abv) * qcs.qty, 2) as overall_gain'),
            DB::raw('ROUND((s.cmp - qcs.abv) / qcs.abv * 100, 2) as gain_pc'),
            DB::raw('ROUND((s.cmp - s.last_day_closing) * qcs.qty, 2) as todays_gain'),
            DB::raw('ROUND(((s.cmp - s.last_day_closing) / s.last_day_closing * 100), 2) as todays_gain_pc'),
            's.day_high as day_high',
            's.day_low as day_low',
            DB::raw('ROUND((s.cmp - qcs.abv) * qcs.qty / '.$this->tot_aum.' * 100, 2) as impact')
        ];

        // $this->agrSelects = array_merge(
        //     $this->selects,
        //     [
        //         DB::raw('SUM(qcs.qty) - SUM(qcs.qty) + '.$tot_aum.' as dlr_aum'),
        //         DB::raw('SUM(qcs.qty) as dlr_qty'),
        //         DB::raw('SUM(qcs.amt_invested) as dlr_amt_invested'),
        //         DB::raw('SUM(s.cmp * qcs.qty) as dlr_cur_value'),
        //         DB::raw('(SUM(qcs.amt_invested) - SUM(s.cmp * qcs.qty)) as dlr_overall_gain'),
        //         DB::raw('(SUM(qcs.amt_invested) - SUM(s.cmp * qcs.qty)) / SUM(s.cmp * qcs.qty) * 100 as dlr_gain_pc'),
        //         DB::raw('SUM(qcs.amt_invested) / '.$tot_aum.' * 100 as dlr_pa'),
        //         DB::raw('SUM(s.cmp * qcs.qty) - SUM(s.last_day_closing * qcs.qty) as dlr_todays_gain'),
        //         DB::raw('(SUM(s.cmp * qcs.qty) - SUM(s.last_day_closing * qcs.qty)) / SUM(s.last_day_closing * qcs.qty) * 100 as dlr_todays_gain_pc')
        //     ]
        // );
        $this->filtersMap = [
            'category' => ['name' => 'qcs.category', 'type' => 'string']
        ];
        $this->sortsMap = [
            'dealer' => ['name' => 'dealer', 'type' => 'string'],
            'dop' => ['name' => 'qcs.dop', 'type' => 'integer'],
            'bse_code' => ['name' => 's.bse_code', 'type' => 'string'],
            'symbol' => ['name' => 's.symbol', 'type' => 'string'],
            'pa' => ['name' => 'qcs.amt_invested / '.$this->tot_aum.' * 100', 'type' => 'integer'],
            'sector' => ['name' => 's.agio_indutry', 'type' => 'string'],
            'tot_qty' => ['name' => 'qcs.qty', 'type' => 'integer'],
            //'abv' => ['name' => 'DB::raw('ROUND(qcs.abv, 2)', 'type' => 'integer']
            //'amt_invested' => ['name' => 'DB::raw('ROUND(qcs.amt_invested, 2)', 'type' => 'integer']
            'cmp' => ['name' => 's.cmp', 'type' => 'integer'],
            // 'cur_value' => ['name' => 'DB::raw('ROUND(s.cmp * qcs.qty, 2)', 'type' => 'integer']
            // 'overall_gain' => ['name' => 'DB::raw(\'ROUND((s.cmp - qcs.abv) * qcs.qty, 2))\'', 'type' => 'integer'],
            // 'gain_pc' => ['name' => 'DB::raw('ROUND((s.cmp - qcs.abv) / qcs.abv * 100, 2)', 'type' => 'integer']
            // 'todays_gain' => ['name' => 'DB::raw('ROUND((s.cmp - s.last_day_closing) * qcs.qty, 2)', 'type' => 'integer']
            'day_high' => ['name' => 's.day_high', 'type' => 'integer'],
            'day_low' => ['name' => 's.day_low', 'type' => 'integer']
        ];

        $this->itemQuery = Script::query();
        $this->relationSelects = [
            'c.id as id',
            'c.client_code as code',
            's.symbol as symbol',
            's.nse_code as nse_code',
            's.cmp as cmp',
            'cs.dp_qty as qty',
            'cs.buy_avg_price as buy_avg_price',
            'c.total_aum as aum',
            'c.category as category',
            DB::raw('cs.buy_avg_price * cs.dp_qty as buy_val'),
            DB::raw('cs.dp_qty * s.cmp as cur_val'),
            DB::raw('(s.cmp - cs.buy_avg_price) * cs.dp_qty as pnl'),
            DB::raw('cs.buy_avg_price * cs.dp_qty * 100 / c.total_aum as pa'),
            DB::raw('(s.cmp - cs.buy_avg_price) / cs.buy_avg_price * 100 as pnl_pc'),
            DB::raw('DATEDIFF(CURDATE(), cs.entry_date) as nof_days'),
            DB::raw('(s.cmp - cs.buy_avg_price) * cs.dp_qty / c.total_aum * 100 as impact'),
            DB::raw('cs.buy_avg_price * cs.dp_qty / c.total_aum * 100 as pa'),
        ];

        // $this->relAgrSelects = array_merge(
        //     $this->relationSelects,
        //     [
        //         DB::raw('SUM(cs.dp_qty) as agr_qty'),
        //         DB::raw('SUM(cs.buy_avg_price * cs.dp_qty) as agr_buy_val'),
        //         DB::raw('SUM(s.cmp * cs.dp_qty) as agr_cur_val'),
        //         DB::raw('SUM((s.cmp - cs.buy_avg_price) * cs.dp_qty) as agr_pnl'),
        //         DB::raw('SUM((s.cmp - cs.buy_avg_price) * cs.dp_qty) / SUM(cs.buy_avg_price * cs.dp_qty) * 100 as agr_pnl_pc'),
        //         DB::raw('SUM((s.cmp - cs.buy_avg_price) * cs.dp_qty) / SUM(c.total_aum) * 100 as agr_impact'),
        //         DB::raw('SUM(cs.buy_avg_price * cs.dp_qty) / SUM(c.total_aum) * 100 as agr_pa'),
        //     ]);

        $this->selIdsKey = 's.id';

        $this->relSortsMap = [
            'id' => ['name' => 'c.id', 'type' => 'integer'],
            'code' => ['name' => 'c.client_code', 'type' => 'string'],
            'symbol' => ['name' => 's.symbol', 'type' => 'string'],
            'cmp' => ['name' => 's.cmp', 'type' => 'float'],
            'qty' => ['name' => 'cs.dp_qty', 'type' => 'integer'],
            'buy_avg_price' => ['name' => 'cs.buy_avg_price', 'type' => 'float'],
            'aum' => ['name' => 'c.total_aum', 'type' => 'float'],
            'category' => ['name' => 'c.category', 'type' => 'string'],
            'buy_val' => ['name' => 'buy_val', 'type' => 'float'],
            'cur_val' => ['name' => 'cur_val', 'type' => 'float'],
            'pnl' => ['name' => 'pnl', 'type' => 'float'],
            'pa' => ['name' => 'pa', 'type' => 'float'],
            'pnl_pc' => ['name' => 'pnl_pc', 'type' => 'float'],
            'nof_days' => ['name' => 'nof_days', 'type' => 'integer'],
            'impact' => ['name' => 'impact', 'type' => 'float'],
        ];

        $this->relUniqueSortKey = 'c.id';

        $this->relSearchesMap = [
            'id' => 'c.id',
            'code' => 'c.client_code',
            'symbol' => 's.symbol',
            'cmp' => 's.cmp',
            'qty' => 'cs.dp_qty',
            'buy_avg_price' => 'cs.buy_avg_price',
            'aum' => 'c.total_aum',
        ];

        $this->relAdvSearchesMap = array_merge(
            $this->relSearchesMap,
            [
                'cur_val' => 'cs.dp_qty * s.cmp',
                'buy_val' => 'cs.buy_avg_price * cs.dp_qty',
                'pnl' => '(s.cmp - cs.buy_avg_price) * cs.dp_qty',
                'pa' => 'cs.buy_avg_price * cs.dp_qty * 100 / c.total_aum',
                'pnl_pc' => '(s.cmp - cs.buy_avg_price) / cs.buy_avg_price * 100',
                'nof_days' => 'DATEDIFF(CURDATE(), cs.entry_date)',
                'impact' => '(s.cmp - cs.buy_avg_price) * cs.dp_qty / c.total_aum * 100',
                'pa' => 'cs.buy_avg_price * cs.dp_qty / c.total_aum * 100',
            ]
        );
        $this->idKey = 'sid';
        $this->relSelIdsKey = 'c.id';
    }

    private function agrSelects()
    {
        return array_merge(
            $this->selects,
            [
                DB::raw('SUM(qcs.qty) - SUM(qcs.qty) + '.$this->tot_aum.' as dlr_aum'),
                DB::raw('SUM(qcs.qty) as dlr_qty'),
                DB::raw('SUM(qcs.amt_invested) as dlr_amt_invested'),
                DB::raw('SUM(s.cmp * qcs.qty) as dlr_cur_value'),
                DB::raw('(SUM(qcs.amt_invested) - SUM(s.cmp * qcs.qty)) as dlr_overall_gain'),
                DB::raw('(SUM(qcs.amt_invested) - SUM(s.cmp * qcs.qty)) / SUM(s.cmp * qcs.qty) * 100 as dlr_gain_pc'),
                DB::raw('SUM(qcs.amt_invested) / '.$this->tot_aum.' * 100 as dlr_pa'),
                DB::raw('SUM(s.cmp * qcs.qty) - SUM(s.last_day_closing * qcs.qty) as dlr_todays_gain'),
                DB::raw('(SUM(s.cmp * qcs.qty) - SUM(s.last_day_closing * qcs.qty)) / SUM(s.last_day_closing * qcs.qty) * 100 as dlr_todays_gain_pc')
            ]
        );
    }

    private function relAgrSelects()
    {
        return array_merge(
            $this->relationSelects,
            [
                DB::raw('SUM(cs.dp_qty) as agr_qty'),
                DB::raw('SUM(cs.buy_avg_price * cs.dp_qty) as agr_buy_val'),
                DB::raw('SUM(s.cmp * cs.dp_qty) as agr_cur_val'),
                DB::raw('SUM((s.cmp - cs.buy_avg_price) * cs.dp_qty) as agr_pnl'),
                DB::raw('SUM((s.cmp - cs.buy_avg_price) * cs.dp_qty) / SUM(cs.buy_avg_price * cs.dp_qty) * 100 as agr_pnl_pc'),
                DB::raw('SUM((s.cmp - cs.buy_avg_price) * cs.dp_qty) / SUM(c.total_aum) * 100 as agr_impact'),
                DB::raw('SUM(cs.buy_avg_price * cs.dp_qty) / SUM(c.total_aum) * 100 as agr_pa'),
            ]
        );
    }

    private function getQuery(): Builder
    {
       $qcs = Client::from('clients', 'c')->userAccessControlled()->join('clients_scripts as cs', 'c.id', '=', 'cs.client_id')->select(
            'c.rm_id as rm_id',
            'c.category as category',
            'cs.script_id as script_id',
            DB::raw('SUM(cs.dp_qty) as qty'),
            DB::raw('SUM(cs.buy_avg_price * cs.dp_qty) as amt_invested'),
            DB::raw('SUM(cs.buy_avg_price * cs.dp_qty) / SUM(cs.dp_qty) as abv'),
            'c.id as client_id',
            DB::raw('MIN(cs.entry_date) as dop')
        )->groupBy('cs.script_id');
        // ->where('cs.dp_qty', '>', 0);


        $query = Script::from('scripts', 's')->joinSub($qcs, 'qcs', 'qcs.script_id', 's.id')
            ->join('users as u', 'u.id', '=', 'qcs.rm_id')->where('s.tracked', 1);

        return $query;

    }
    protected function getRelationQuery(int $id = null)
    {
        $query = DB::table('scripts', 's')
            ->join('clients_scripts as cs', 's.id', '=', 'cs.script_id')
            ->join('clients as c', 'c.id', '=', 'cs.client_id')
            ->join('users as u', 'c.rm_id', '=', 'u.id')
            ->where('s.id', $id)
            ->where('cs.dp_qty', '>', 0);

        $user = $user ?? auth()->user();
        if ($user->hasRole('Dealer')) {
            $query->whereIn('c.rm_id', [$user->id]);
        } else if ($user->hasRole('Team Leader')) {
            $dealers =  array_values(User::where('teamleader_id', $user->id)->pluck('id')->toArray());
            $dealers[] = $user->id;
            $query->whereIn('c.rm_id', $dealers);
        }

        return $query;
    }

    public function getList($search)
    {
        $scripts = Script::where('symbol', 'like', $search.'%')
            ->orWhere('company_name', 'like', '%'.$search.'%')
            ->orWhere('isin_code', 'like', '%'.$search)
            ->select(['id', 'isin_code as code', 'symbol', 'company_name'])
            ->limit(15)->get();
        return [
            'scripts' => $scripts
        ];
    }

    protected function accessCheck($item): bool
    {
        return true;
    }

    public function formatIndexResults(mixed $results): array
    {
        $formatted = [];
        foreach ($results as $result) {
            $row = $result;
            $formatted[] = $row;
        }
        return $formatted;
    }
    public function formatRelationResults(mixed $results): array
    {
        $formatted = [];
// dd($results[0]['cmp']);
        foreach ($results as $result) {
            $row = [];
            if (is_array($result)) {
                $row = $result;
            } else {
                $row['id'] = $result->id;
                $row['code'] = $result->code;
                $row['symbol'] = $result->symbol;
                $row['category'] = $result->category;
                $row['qty'] = $result->qty;
                $row['buy_avg_price'] = round($result->buy_avg_price, 2);
                $row['buy_val'] = round($result->buy_val, 2);
                $row['cmp'] = round($result->cmp, 2);
                $row['cur_val'] = round($result->cur_val, 2);
                $row['pnl'] = round($result->pnl, 2);
                $row['pnl_pc'] = round($result->pnl_pc, 2);
                $row['nof_days'] = $result->nof_days;
                $row['impact'] = round($result->impact, 2);
                $row['pa'] = round($result->pa, 2);
            }

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
        $p = round($price * (100 - $slippage) / 100, 2);
        $p = round($p * 20) / 20; // round(($p * 100) / 5) * 5 / 100

        foreach ($results as $result) {
            $row = [
                'exchange_code' => 'NSE',
                'buyorsell' => 'S',
                'product' => 'SellFromDP',
                'order_type' => 'L',
                'discount_qty' => '0',
                'trigger_price' => '0'
            ];

            $row['script_name'] = $result->symbol.' EQ| '.$result->nse_code;
            $row['qty'] = round(($result->qty * $qty /100)) . '';
            $row['lot'] = round(($result->qty * $qty /100)) . '';
            $row['price'] = $p . '';
            $row['client_code'] = $result->code;
            $export[] = $row;
        }
        return $export;
    }

    protected function preIndexExtra(): void
    {
        $this->pa = 0;
    }

    // private function getFilterParams($query, $filters) {
    //     return [];
    // }

    public function update($id, $data)
    {
        return Script::where('id', $id)->update($data);
    }
}

?>
