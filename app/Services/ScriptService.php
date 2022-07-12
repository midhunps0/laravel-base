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
            's.cmp as cmp',
            'cs.dp_qty as qty',
            'cs.buy_avg_price as buy_avg_price',
            'c.total_aum as aum',
            DB::raw('cs.buy_avg_price * cs.dp_qty as buy_val'),
            DB::raw('cs.dp_qty * s.cmp as cur_val'),
            DB::raw('(s.cmp - cs.buy_avg_price) * cs.dp_qty as pnl'),
            DB::raw('cs.buy_avg_price * cs.dp_qty * 100 / c.total_aum as pa'),
            DB::raw('(s.cmp - cs.buy_avg_price) / cs.buy_avg_price * 100 as pnl_pc'),
            DB::raw('DATEDIFF(CURDATE(), cs.entry_date) as nof_days'),
            DB::raw('(s.cmp - cs.buy_avg_price) * cs.dp_qty / c.total_aum * 100 as impact'),
            DB::raw('cs.buy_avg_price * cs.dp_qty / c.total_aum * 100 as pa'),
        ];
        $this->selIdsKey = 'c.id';

        $this->relSortsMap = [
            'id' => ['name' => 'c.id', 'type' => 'integer'],
            'code' => ['name' => 'c.client_code', 'type' => 'string'],
            'symbol' => ['name' => 's.symbol', 'type' => 'string'],
            'cmp' => ['name' => 's.cmp', 'type' => 'float'],
            'qty' => ['name' => 'cs.dp_qty', 'type' => 'integer'],
            'buy_avg_price' => ['name' => 'cs.buy_avg_price', 'type' => 'float'],
            'aum' => ['name' => 'c.total_aum', 'type' => 'float'],
            'buy_val' => ['name' => 'buy_val', 'type' => 'float'],
            'cur_val' => ['name' => 'cur_val', 'type' => 'float'],
            'pnl' => ['name' => 'pnl', 'type' => 'float'],
            'pa' => ['name' => 'pa', 'type' => 'float'],
            'pnl_pc' => ['name' => 'pnl_pc', 'type' => 'float'],
            'nof_days' => ['name' => 'nof_days', 'type' => 'integer'],
            'impact' => ['name' => 'impact', 'type' => 'float'],
        ];

        $this->relUniqueSortKey = 'c.id';
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
                $row['qty'] = $result->qty;
                $row['buy_avg_price'] = $result->buy_avg_price;
                $row['buy_val'] = $result->buy_val;
                $row['cmp'] = $result->cmp;
                $row['cur_val'] = $result->cur_val;
                $row['pnl'] = $result->pnl;
                $row['pnl_pc'] = $result->pnl_pc;
                $row['nof_days'] = $result->nof_days;
                $row['impact'] = $result->impact;
                $row['pa'] = $result->pa;
            }

            $formatted[] = $row;
        }

        return $formatted;
    }

    private function getFilterParams($query, $filters) {
        return [];
    }
}

?>