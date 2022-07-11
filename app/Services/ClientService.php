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

class ClientService implements ModelViewConnector
{
    use IsModelViewConnector;

    public function __construct()
    {
        /******* Index query *******/

        $indexScriptQ = DB::table('clients_scripts as cs')
            ->join('scripts as s', 's.id', '=', 'cs.script_id')
            ->select(
                'cs.client_id as cid',
                's.id as sid',
                's.cmp as cmp',
                'cs.buy_avg_price as buy_avg_price',
                'cs.dp_qty as dp_qty',
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
            ->joinSub($indexScriptQ, 'cst', 'c.id' , '=', 'cst.cid', 'left')
            ->joinSub($indexLBQ, 'lbq', 'c.id' , '=', 'lbq.lbcs_client_id', 'left')
            ->join('users as u', 'c.rm_id', '=', 'u.id');

        $this->selects = [
            'c.id as id',
            'c.rm_id as rm_id',
            'c.name as name',
            'c.client_code as client_code',
            'u.name as dealer',
            'c.total_aum as aum',
            'c.realised_pnl as realised_pnl',
            'cst.cur_value as cur_value',
            DB::raw('(cst.cmp - cst.buy_avg_price) * cst.dp_qty as pnl'),
            DB::raw('(cst.cmp - cst.buy_avg_price) * cst.dp_qty / (cst.buy_avg_price * cst.dp_qty) * 100 as pnl_pc'),
            'cst.allocated_aum as allocated_aum',
            'lbq.liquidbees as liquidbees',
            DB::raw('c.total_aum - cst.allocated_aum as cash'),
            DB::raw('(c.total_aum - cst.allocated_aum) / c.total_aum * 100 as cash_pc'),
            DB::raw('c.realised_pnl + cst.cur_value - c.total_aum as returns'),
            DB::raw('(c.realised_pnl + cst.cur_value - c.total_aum) /c.total_aum * 100 as returns_pc'),
        ];

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
            'cs.entry_date as entry_date',
            's.symbol as symbol',
            's.industry as category',
            's.mvg_sector as sector',
            'cs.dp_qty as qty',
            'cs.buy_avg_price as buy_avg_price',
            DB::raw('cs.buy_avg_price * cs.dp_qty as amt_invested'),
            's.cmp as cmp',
            DB::raw('s.cmp * cs.dp_qty as cur_value'),
            DB::raw('(s.cmp - cs.buy_avg_price) * cs.dp_qty as overall_gain'),
            DB::raw('(s.cmp - cs.buy_avg_price) / cs.buy_avg_price * 100  as pc_change'),
            's.last_day_closing as ldc',
            DB::raw('(s.cmp - s.last_day_closing) * cs.dp_qty as todays_gain'),
            's.day_high as day_high',
            's.day_low as day_low',
            DB::raw('((s.cmp - cs.buy_avg_price) * cs.dp_qty) / c.total_aum * 100 as impact'),
            DB::raw('DATEDIFF(CURDATE(), cs.entry_date) as nof_days'),
            DB::raw('cs.buy_avg_price * cs.dp_qty * 100 / c.total_aum as pa'),
        ];
        $this->relSearchesMap = [
            'aum' => 'c.total_aum',
            'entry_date' => 'cs.entry_date',
            'symbol' => 's.symbol',
            'category' => 's.industry',
            'sector' => 's.mvg_sector',
            'cmp' => 's.cmp',
            'dp_qty' => 'cs.dp_qty',
            'buy_avg_price', 'cs.buy_avg_price',
            'day_high' => 's.day_high',
            'day_low' => 's.day_low'
        ];
        $this->idKey = 'id';
        $this->selIdsKey = 's.id';
    }

    protected function applyGroupings($query) {
        return $query->groupBy('id');
    }

    protected function getRelationQuery(int $id = null)
    {
        return Client::from('clients as c')
            ->join('clients_scripts as cs', 'c.id' , '=', 'cs.client_id')
            ->join('scripts as s', 's.id', '=', 'cs.script_id')
            ->where('c.id', $id);
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
            $row['total_aum'] = $aum;
            $row['allocated_aum'] = $al_aum ?? 0;
            $row['cur_value'] = $cv ?? 0;

            $formatted[] = $row;
        }
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
                $row['aum'] = $aum;
                $row['entry_date'] = $result->entry_date;
                $row['symbol'] = $result->symbol;
                $row['category'] = $result->category;
                $row['sector'] = $result->sector;
                $row['qty'] = $result->qty;
                $row['buy_avg_price'] = $result->buy_avg_price;
                $row['amt_invested'] = $result['amt_invested'];
                $row['cmp'] = $result->cmp;
                $row['cur_value'] = $result->cur_value;
                $row['ldc'] = $result->ldc;
                $row['day_high'] = $result->day_high;
                $row['day_low'] = $result->day_low;
                $row['nof_days'] = $result->nof_days;
                $row['pa'] = $result->pa;
                $row['overall_gain'] = $result['overall_gain'];
                $row['pc_change'] = $result['pc_change'];
                $row['todays_gain'] = $result['todays_gain'];
                $row['impact'] = $result['impact'];
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

    private function getFilterParams($query, $filters) {
        return [];
    }
}

?>