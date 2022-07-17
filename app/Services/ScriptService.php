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

    public function __construct()
    {
        $this->query = Script::query()->userAccessControlled();
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

        $this->relAgrSelects = array_merge(
            $this->relationSelects,
            [
                DB::raw('SUM(cs.buy_avg_price * cs.dp_qty) as agr_buy_val'),
                DB::raw('SUM(s.cmp * cs.dp_qty) as agr_cur_val'),
                DB::raw('SUM((s.cmp - cs.buy_avg_price) * cs.dp_qty) as agr_pnl'),
                DB::raw('SUM((s.cmp - cs.buy_avg_price) * cs.dp_qty) / SUM(cs.buy_avg_price * cs.dp_qty) * 100 as agr_pnl_pc'),
                DB::raw('SUM((s.cmp - cs.buy_avg_price) * cs.dp_qty) / SUM(c.total_aum) * 100 as agr_impact'),
                DB::raw('SUM(cs.buy_avg_price * cs.dp_qty) / SUM(c.total_aum) * 100 as agr_pa'),
            ]);

        $this->selIdsKey = 'c.id';

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
        $this->relSelIdsKey = 'c.id';
    }

    protected function getRelationQuery(int $id = null)
    {
        $query = DB::table('scripts', 's')
            ->join('clients_scripts as cs', 's.id', '=', 'cs.script_id')
            ->join('clients as c', 'c.id', '=', 'cs.client_id')
            ->join('users as u', 'c.rm_id', '=', 'u.id')
            ->where('s.id', $id);

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
            $row['price'] = round($price * (100 - $slippage) / 100, 2) . '';
            $row['client_code'] = $result->code;
            $export[] = $row;
        }
        return $export;
    }

    private function getFilterParams($query, $filters) {
        return [];
    }
}

?>