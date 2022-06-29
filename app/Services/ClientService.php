<?php
namespace App\Services;

use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use App\Contracts\ModelViewConnector;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Database\Query\Builder;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class ClientService implements ModelViewConnector
{
    use IsModelViewConnector;

    private $scriptsSelects;
    public function __construct()
    {
        $this->query = Client::query()->userAccessControlled();
        $this->itemQuery = Client::query();
        $this->scriptsSelects = [
            's.id as id',
            'cs.entry_date as entry_date',
            's.symbol',
            's.industry as category',
            's.mvg_sector as sector',
            'cs.dp_qty as qty',
            'cs.buy_avg_price as buy_avg_price',
            DB::raw('cs.buy_avg_price * cs.dp_qty as amt_invested'),
            DB::raw('round(cs.buy_avg_price * cs.dp_qty * 100 / c.total_aum, 2) as pa'),
        ];
    }

    protected function relationQuery(int $id = null)
    {
        $query = DB::table('clients', 'c')
            ->join('clients_scripts as cs', 'c.id', '=', 'cs.client_id')
            ->join('scripts as s', 's.id', '=', 'cs.script_id')
            ->join('users as u', 'c.rm_id', '=', 'u.id')
            ->where('c.id', $id);
        $user = auth()->user();
        if($user->hasRole('Dealer')) {
            $query->where('c.rm_id', $user->id);
        } else if($user->hasRole('Dealer')) {
            $dealers = array_values(User::where('teamleader_id', $user->id)->pluck('id')->toArray());
            $dealers[] = $user->id;
            $query->whereIn('c.rm_id', $dealers);
        }
        return $query;
    }

    public function getList($search)
    {
        $clients = Client::where('client_code', 'like', $search.'%')
            ->orWhere('name', 'like', '%'.$search.'%')->select(['id', 'client_code as code', 'name'])->userAccessControlled()->limit(15)->get();
        return [
            'clients' => $clients
        ];
    }

    protected function accessCheck(int $id): bool
    {
        $client = $this->itemQuery->find($id);
        $user = auth()->user();
        if($user->hasRole('Dealer')) {
            if ($client->rm_id != $user->id) {
                throw new AccessDeniedException('You are not allowed to view this client');
            }
        } else if($user->hasRole('Team Leader')) {
            $dealers = array_values(User::where('teamleader_id', $user->id)->pluck('id')->toArray());
            $dealers[] = $user->id;
            if (!in_array($client->rm_id, $dealers)) {

            dd('error', $dealers, $client);
                throw new AccessDeniedException('You are not allowed to view this client');
            }
        }
        return true;
    }

    private function getFilterParams($query, $filters) {
        return [];
    }
}

?>