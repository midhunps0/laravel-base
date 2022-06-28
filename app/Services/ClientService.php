<?php
namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Facades\DB;
use App\Contracts\ModelViewConnector;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Database\Query\Builder;

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
            's.symbol',
            's.industry as category',
            's.mvg_sector as sector',
            'cs.dp_qty as qty',
            'cs.buy_avg_price as buy_avg_price',
            DB::raw('cs.buy_avg_price * cs.dp_qty as amt_invested'),
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
        $client = $this->itemQuery->find($id);
        $query = DB::table('clients', 'c')
            ->join('clients_scripts as cs', 'c.id', '=', 'cs.client_id')
            ->join('scripts as s', 's.id', '=', 'cs.script_id')
            ->where('c.id', $client->id);
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
            'model' => $client,
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

    private function getFilterParams($query, $filters) {
        return [];
    }
}

?>