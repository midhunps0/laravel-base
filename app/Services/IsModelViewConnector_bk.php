<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\Request;
use Symfony\Component\Finder\Exception\AccessDeniedException;

trait IsModelViewConnector{
    protected $query;
    protected $itemQuery;
    protected $relationQuery;
    protected $idKey = 'id';
    protected $relIdKey = 'id';
    protected $selects = '*';
    protected $relationSelects = '*';
    protected $agrSelects = '*';
    protected $relAgrSelects = '*';
    protected $selIdsKey = 'id';
    protected $relSelIdsKey = 'id';
    protected $searchesMap = [];
    protected $relSearchesMap = [];
    protected $advSearchesMap = [];
    protected $relAdvSearchesMap = [];
    protected $sortsMap = [];
    protected $relSortsMap = [];
    protected $filtersMap = [];
    protected $relFiltersMap = [];
    protected $uniqueSortKey = null;
    protected $relUniqueSortKey = null;

    public function index(
        int $itemsCount,
        ?int $page,
        array $searches,
        array $sorts,
        array $filters,
        array $advSearch,
        string $selectedIds,
        string $resultsName = 'results'
    ): array {

        $this->preIndexExtra();

        $queryData = $this->getQueryAndParams(
            $searches,
            $sorts,
            $filters,
            $advSearch,
            $selectedIds
        );

        DB::statement("SET SQL_MODE=''");
// dd($queryData['query']->select($this->selects)->toSql());
        $results = $queryData['query']->paginate(
            $itemsCount,
            $this->selects,
            'page',
            $page
        );

        // dd($agrQuery['query']->select($this->agrSelects)->toSql());
        $aggregates = $queryData['query']->select($this->agrSelects)->get()->first();

        DB::statement("SET SQL_MODE='only_full_group_by'");
// dd($results->toArray());
        // $itemIds = $results->pluck('id')->toArray();
        $data = $results->toArray();

        $paginator = $this->getPaginatorArray($results);
        return [
            $resultsName => $results,
            'results_json' => json_encode($this->formatIndexResults($results->toArray()['data'])),
            'aggregates' => json_encode($aggregates),
            'params' => $queryData['searchParams'],
            'sort' => $queryData['sortParams'],
            'filter' => $queryData['filterData'],
            'items_count' => $itemsCount,
            'items_ids' => $this->getItemIds($results),
            'total_results' => $data['total'],
            'current_page' => $data['current_page'],
            'paginator' => json_encode($paginator),
            'route' => Request::route()->getName()
        ];
    }

    private function getItemIds($results) {
        $ids = $results->pluck($this->idKey)->toArray();
        return json_encode($ids);
    }

    public function processDownload(
        array $searches,
        array $sorts,
        array $filters,
        array $advParams,
        string $selectedIds
    ): array {
        $queryData = $this->getQueryAndParams(
            $searches,
            $sorts,
            $filters,
            $advParams,
            $selectedIds
        );

        DB::statement("SET SQL_MODE=''");
        $results = $queryData['query']->select($this->selects)->get();
        DB::statement("SET SQL_MODE='only_full_group_by'");

        // dd($this->formatIndexResults($results));
        return $this->formatIndexResults($results);
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

        $results = $queryData['query']->select($this->selects)->get()->pluck($this->idKey)->unique()->toArray();
        DB::statement("SET SQL_MODE='only_full_group_by'");
        return $results;
    }

    public function getQueryAndParams(
        array $searches,
        array $sorts,
        array $filters,
        array $advSearch = [],
        string $selectedIds = ''
    ): array {
        $filterData = $this->getFilterParams($this->query, $filters);
        $searchParams = $this->getSearchParams($this->query, $searches);
        $sortParams = $this->getSortParams($this->query, $sorts);
        $advParams = $this->getAdvParams($this->query, $advSearch);

        $this->extraConditions($this->query);

        if (isset($selectedIds) && strlen(trim($selectedIds)) > 0) {
            $ids = explode('|', $selectedIds);
            // $this->query->whereIn('c.id', $ids);
            $this->querySelectedIds($this->query, $this->selIdsKey, $ids);
        }

        return [
            'query' => $this->query,
            'searchParams' => $searchParams,
            'sortParams' => $sortParams,
            'filterData' => $filterData,
            'advparams' => $advParams
        ];
    }

    public function getItem(string $arg): Collection
    {
        return $this->itemQuery->find($arg);
    }

    public function processShowDownload(
        int $id,
        array $searches,
        array $sorts,
        array $filters,
        array $advSearches,
        string $selectedIds,
    ): array {
        $queryData = $this->getRelationQueryAndParams(
            $this->getRelationQuery($id),
            $searches,
            $sorts,
            $filters,
            $advSearches,
            $selectedIds,
            $this->selIdsKey
        );

        DB::statement("SET SQL_MODE=''");
        $results = $queryData['query']->select($this->relationSelects)->get();
        DB::statement("SET SQL_MODE='only_full_group_by'");

        return $this->formatRelationResults($results);
    }

    public function getShowIdsForParams(
        int $id,
        array $searches,
        array $sorts,
        array $filters,
        array $advSearch
    ): array {
        $queryData = $this->getRelationQueryAndParams(
            $this->getRelationQuery($id),
            $searches,
            $sorts,
            $filters,
            $advSearch
        );

        $results = $queryData['query']->select($this->relationSelects)->get()->pluck($this->relIdKey)->toArray();
        return $results;
    }

    public function getShowData(
        int $id,
        int $itemsCount = 10,
        ?int $page = 1,
        array $searches = [],
        array $sorts = [],
        array $filters = [],
        array $advSearch,
        string $selectedIds = '',
        string $relationsResultsName = 'results'
    ) {
        $this->preRelExtra();
        $item = $this->itemQuery->find($id);
        if (!$this->accessCheck($item)) {
            throw new AccessDeniedException('You are not allowed to view this client');
        }
        $query = $this->getRelationQuery($item->id);
        $queryData = $this->getRelationQueryAndParams(
            $query,
            $searches,
            $sorts,
            $filters,
            $advSearch,
            $selectedIds
        );
        // dd($queryData['query']->select($this->relationSelects)->toSql());
        DB::statement("SET SQL_MODE=''");
        $relatedResults = $queryData['query']->paginate(
            $itemsCount,
            $this->relationSelects,
            'page',
            $page
        );
        $aggregates = $queryData['query']->select($this->relAgrSelects)->get()->first();
        DB::statement("SET SQL_MODE='only_full_group_by'");

        // $itemIds = $relatedResults->pluck('id')->toArray();
        $data = $relatedResults->toArray();

        $paginator = $paginator = $this->getPaginatorArray($relatedResults);

        return [
            'model' => $item,
            $relationsResultsName => $relatedResults,
            'results_json' => json_encode($this->formatRelationResults($data['data'])),
            'aggregates' => json_encode($aggregates),
            'params' => $queryData['searchParams'],
            'sort' => $queryData['sortParams'],
            'filter' => $queryData['filterData'],
            'items_count' => $itemsCount,
            'items_ids' => $this->getItemIds($relatedResults),
            'total_results' => $data['total'],
            'current_page' => $data['current_page'],
            'paginator' => json_encode($paginator),
            'route' => Request::route()->getName()
        ];
    }

    public function getRelationQueryAndParams(
        Builder $query,
        array $searches,
        array $sorts,
        array $filters,
        array $advSearch,
        string $selectedIds = ''): array
    {
        $filterData = $this->getFilterParams($query, $filters);
        $searchParams = $this->getSearchParams($query, $searches, 'relation');
        $sortParams = $this->getSortParams($query, $sorts);
        $this->getAdvParams($query, $advSearch, 'relation');

        $this->extraRelationConditions($query);

        if (strlen(trim($selectedIds)) > 0) {
            $ids = explode('|', $selectedIds);
            $this->querySelectedIds($query, $this->relSelIdsKey, $ids);
        }

        return [
            'query' => $query,
            'searchParams' => $searchParams,
            'sortParams' => $sortParams,
            'filterData' => $filterData
        ];
    }

    private function querySelectedIds(Builder $query, string $idKey, array $ids): void
    {
        $query->whereIn($idKey, $ids);
    }
    abstract protected function getRelationQuery(int $id = null);

    abstract protected function accessCheck(Model $item): bool;

    private function getSearchOperator($op, $val)
    {
        $ops = [
            'ct' => 'like',
            'st' => 'like',
            'en' => 'like',
            'gt' => '>',
            'lt' => '<',
            'gte' => '>=',
            'lte' => '<=',
            'eq' => '=',
            'neq' => '<>',
        ];
        $v = $val;
        switch($op) {
            case 'ct':
                $v = '%'.$val.'%';
                break;
            case 'st':
                $v = $val.'%';
                break;
            case 'en':
                $v = '%'.$val;
                break;
        }
        if (in_array($op, ['gt', 'lt', 'gte', 'lte','eq', 'neq'])) {
            $v = floatval($v);
        }
        return [
            'op' => $ops[$op],
            'val' => $v
        ];
    }

    private function getAdvParams($query, array $advSearches, string $searchType = 'index'): array
    {
        $map = $searchType == 'index' ? $this->advSearchesMap : $this->relAdvSearchesMap;

        $searchParams = [];
        foreach ($advSearches as $search) {
            $data = explode('::', $search);
            $key = $map[$data[0]] ?? $data[0];
            $op = $this->getSearchOperator($data[1], $data[2]);
            // dd($key, $op);
            // $query->having($key, $op['op'], $op['val']);
            $query->whereRaw($key.' '.$op['op'].' \''.$op['val'].'\'');
            $searchParams[$data[0]] = $data[1];
        }
        return $searchParams;
    }

    private function getSearchParams($query, array $searches, string $searchType = 'index'): array
    {
        $map = $searchType == 'index' ? $this->searchesMap : $this->relSearchesMap;
        $searchParams = [];
        foreach ($searches as $search) {
            $data = explode('::', $search);
            $key = $map[$data[0]] ?? $data[0];
            $query->where($key, 'like', '%'.$data[1].'%');
            $searchParams[$data[0]] = $data[1];
        }
        return $searchParams;
    }

    private function getSortParams($query, array $sorts, string $sortType = 'index'): array
    {
        $map = $sortType == 'index' ? $this->sortsMap : $this->relSortssMap;
        $usortkey = $sortType == 'index' ? $this->uniqueSortKey : $this->relUniqueSortKey;

        $sortParams = [];
        foreach ($sorts as $sort) {
            $data = explode('::', $sort);
            $key = $map[$data[0]] ?? $data[0];
            // $query->orderBy($key, $data[1]);
            if (isset($usortkey) && isset($map[$data[0]])) {
                $type = $key['type'];
                $kname = $key['name'];
                switch ($type) {
                    case 'string';
                        $query->orderByRaw('CONCAT('.$kname.',\'::\','.$usortkey.') '.$data[1]);
                        break;
                    case 'integer';
                        $query->orderByRaw('CONCAT(LPAD(ROUND('.$kname.',0),20,\'00\'),\'::\','.$usortkey.') '.$data[1]);
                        break;
                    case 'float';
                        $query->orderByRaw('CONCAT( LPAD(ROUND('.$kname.',0) * 100,20,\'00\') ,\'::\','.$usortkey.') '.$data[1]);
                        break;
                    default:
                        $query->orderByRaw('CONCAT('.$kname.'\'::\','.$usortkey.') '.$data[1]);
                        break;
                }
            } else {
                $query->orderBy($data[0], $data[1]);
            }
            // $sortParams[$data[0]] = $data[1];
        }
        // dd($sortParams);
        return $sortParams;
    }

    private function getFilterParams($query, array $filters, string $sortType = 'index'): array
    {
        $filterData = [];
        $map = $sortType == 'index' ? $this->filtersMap : $this->relFiltersMap;

        foreach ($filters as $filter) {
            $data = explode('::', $filter);
            $key = $map[$data[0]] ?? $data[0];
            $filterData[$data[0]] = $data[1];
            if (isset($map[$data[0]])) {
                $type = $key['type'];
                $kname = $key['name'];
                switch ($type) {
                    case 'string';
                        $query->where($kname, 'like', $data[1]);
                        break;
                    default:
                        $query->where($kname, $data[1]);
                        break;
                }
            } else {
                $query->where($data[0], $data[1]);
            }

            // $filterData[$data[0]]['selected'] = $data[1];
        }

        return $filterData;
    }

    private function getPaginatorArray(LengthAwarePaginator $results): array
    {
        $data = $results->toArray();
        return [
            'currentPage' => $data['current_page'],
            'totalItems' => $data['total'],
            'lastPage' => $data['last_page'],
            'itemsPerPage' => $results->perPage(),
            'nextPageUrl' => $results->nextPageUrl(),
            'prevPageUrl' => $results->previousPageUrl(),
            'elements' => $results->links()['elements'],
            'firstItem' => $results->firstItem(),
            'lastItem' => $results->lastItem(),
            'count' => $results->count(),
        ];
    }

    protected function extraConditions(Builder $query): void {}
    protected function extraRelationConditions(Builder $query): void {}
    protected function applyGroupings(Builder $q)
    {
        return $q;
    }

    protected function formatIndexResults(array $results): array
    {
        $formatted = [];
        foreach ($results as $result) {
            if(is_array($result)) {
                $formatted[] = $result;
            } else {
                $formatted['id'] = $result->id;
                $formatted['rm_id'] = $result->rm_id;
                $formatted['name'] = $result->name;
                $formatted['client_code'] = $result->client_code;
                $formatted['dealer'] = $result->dealer;
                $formatted['aum'] = $result->aum;
                $formatted['realised_pnl'] = $result->realised_pnl;
                $formatted['cur_value'] = $result->cur_value;
                $formatted['pnl'] = $result->pnl;
                $formatted['pnl_pc'] = $result->pnl_pc;
                $formatted['allocated_aum'] = $result->allocated_aum;
                $formatted['pa'] = $result->pa;
                $formatted['liquidbees'] = $result->liquidbees;
                $formatted['cash'] = $result->cash;
                $formatted['cash_pc'] = $result->cash_pc;
                $formatted['returns'] = $result->returns;
                $formatted['returns_pc'] = $result->returns_pc;
            }
        }
        return $formatted;
    }

    protected function formatRelationResults(array $results): array
    {
        return $results;
    }

    protected function preIndexExtra(): void {}
    protected function preRelExtra(): void {}
}
?>