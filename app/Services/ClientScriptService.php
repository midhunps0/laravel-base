<?php
namespace App\Services;

use App\Models\User;
use App\Models\Client;
// use App\Models\Script;
use Illuminate\Support\Facades\DB;
use App\Contracts\ModelViewConnector;
use App\Helpers\AppHelper;

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
            ->join('clients_scripts as clsc', 'c.id', '=', 'clsc.client_id', 'left')
            ->join('scripts as s', 's.id', '=', 'clsc.script_id', 'left')
            ->joinSub($indexScriptQ, 'cst', 'c.id' , '=', 'cst.cid', 'left')
            ->joinSub($indexLBQ, 'lbq', 'c.id' , '=', 'lbq.lbcs_client_id', 'left')
            ->join('users as u', 'c.rm_id', '=', 'u.id', 'left');

        $this->selects = [
            'c.id as id',
            'c.rm_id as rm_id',
            'c.name as name',
            'c.client_code as client_code',
            'u.name as dealer',
            'c.total_aum as aum',
            'c.realised_pnl as realised_pnl',
            'cst.cur_value as client_cur_value',
            's.id as sid',
            's.symbol as symbol',
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
        ];

        $this->searchesMap = [
            'id' => 'c.id',
            'rm_id' => 'c.rm_id',
            'name' => 'c.name',
            'client_code' => 'c.client_code',
            'dealer' => 'u.name',
            'aum' => 'c.total_aum',
            'realised_pnl' => 'c.realised_pnl',
            'client_cur_value' => 'cst.cur_value',
            'sid' => 's.id',
            'symbol' => 's.symbol',
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

        $this->selIdsKey = 'c.id';
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
            $aum = $result['total_aum'] ?? 0;
            $cv = $result['client_cur_value'];
            $al_aum = $result['allocated_aum'];

            $row = $result;
            $row['total_aum'] = $aum;
            $row['allocated_aum'] = $al_aum ?? 0;
            $row['client_cur_value'] = $cv ?? 0;

            $formatted[] = $row;
        }
        return $formatted;
    }

    private function getFilterParams($query, $filters) {
        return [];
    }
}

?>