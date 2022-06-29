<?php
namespace App\Services;

use App\Models\Client;
use App\Models\Script;
use Illuminate\Support\Facades\DB;
use App\Contracts\ModelViewConnector;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Database\Query\Builder;

class ScriptService implements ModelViewConnector
{
    use IsModelViewConnector;

    private $scriptsSelects;
    public function __construct()
    {
        $this->query = Script::query()->userAccessControlled();
        $this->itemQuery = Script::query();
        $this->scriptsSelects = [
            'c.id as id',
            'c.name as client_name',
            's.symbol',
            'cs.dp_qty as qty',
            'cs.buy_avg_price as buy_avg_price',
            DB::raw('cs.buy_avg_price * cs.dp_qty as avg_buy_value'),
            DB::raw('cs.buy_avg_price * cs.dp_qty * 100 / c.total_aum as pa'),
        ];
    }

    public function getShowData(
        int $id,
        int $itemsCount = 10,
        ?int $page = 1,
        array $searches = [],
        array $sorts = [],
        array $filters = [],
        string $selectedIds = '',
        $relationsResultsName = 'results'
    ) {
        $script = $this->itemQuery->find($id);
        $query = DB::table('scripts', 's')
            ->join('clients_scripts as cs', 's.id', '=', 'cs.script_id')
            ->join('clients as c', 'c.id', '=', 'cs.client_id')
            ->where('s.id', $script->id);
        $queryData = $this->getRelationQueryAndParams(
            $query,
            $searches,
            $sorts,
            $filters,
            $selectedIds
        );
// dd($queryData['query']->toSql());
        $scripts = $queryData['query']->paginate(
            $itemsCount,
            $this->scriptsSelects,
            'page',
            $page
        );

        // dd($scripts);
        $itemIds = $scripts->pluck('id')->toArray();
        $data = $scripts->toArray();
        return [
            'model' => $script,
            $relationsResultsName => $scripts,
            'params' => $queryData['searchParams'],
            'sort' => $queryData['sortParams'],
            'filter' => $queryData['filterData'],
            'items_count' => $itemsCount,
            'items_ids' => implode(',',$itemIds),
            'total_results' => $data['total'],
            'current_page' => $data['current_page']
        ];
    }

    public function getRelationQueryAndParams(
        Builder $query,
        array $searches,
        array $sorts,
        array $filters,
        string $selectedIds = ''): array
    {
        $filterData = $this->getFilterParams($query, $filters);
        $searchParams = $this->getSearchParams($query, $searches);
        $sortParams = $this->getSortParams($query, $sorts);

        if (strlen(trim($selectedIds)) > 0) {
            $ids = explode('|', $selectedIds);
            $query->whereIn('id', $ids);
        }

        return [
            'query' => $query,
            'searchParams' => $searchParams,
            'sortParams' => $sortParams,
            'filterData' => $filterData
        ];
    }

    public function getList($search)
    {
        $clients = Client::where('client_code', 'like', $search.'%')
            ->orWhere('name', 'like', '%'.$search.'%')->select(['id', 'client_code as code', 'name'])->limit(15)->get();
        return [
            'clients' => $clients
        ];
    }

    private function getFilterParams($query, $filters) {
        return [];
    }
}

?>