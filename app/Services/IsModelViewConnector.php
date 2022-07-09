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
    protected $selects = '*';
    protected $relationSelects;
    protected $selIdsKey = 'id';
    protected $searchesMap = [];
    protected $relSearchesMap = [];

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
        $queryData = $this->getQueryAndParams(
            $searches,
            $sorts,
            $filters,
            $advSearch,
            $selectedIds
        );

        DB::statement("SET SQL_MODE=''");
        $results = $queryData['query']->paginate(
            $itemsCount,
            $this->selects,
            'page',
            $page
        );
        DB::statement("SET SQL_MODE='only_full_group_by'");
// dd($results);
        $itemIds = $results->pluck('id')->toArray();
        $data = $results->toArray();

        $paginator = $this->getPaginatorArray($results);
        return [
            $resultsName => $results,
            'results_json' => json_encode($this->formatIndexResults($results->toArray()['data'])),
            'params' => $queryData['searchParams'],
            'sort' => $queryData['sortParams'],
            'filter' => $queryData['filterData'],
            'items_count' => $itemsCount,
            'items_ids' => implode(',', $itemIds),
            'total_results' => $data['total'],
            'current_page' => $data['current_page'],
            'paginator' => json_encode($paginator),
            'route' => Request::route()->getName()
        ];
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
        $results = $queryData['query']->get();
        DB::statement("SET SQL_MODE='only_full_group_by'");
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

        $results = $queryData['query']->get()->pluck('id');
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

        if (strlen(trim($selectedIds)) > 0) {
            $ids = explode('|', $selectedIds);
            $this->query->whereIn('c.id', $ids);
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
        array $searches,
        array $sorts,
        array $filters,
        array $advSearch
    ): array {
        $queryData = $this->getRelationQueryAndParams(
            $this->relationQuery,
            $searches,
            $sorts,
            $filters,
            $advSearch
        );

        $results = $queryData['query']->get()->pluck('id');
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

        DB::statement("SET SQL_MODE=''");
        $relatedResults = $queryData['query']->paginate(
            $itemsCount,
            $this->relationSelects,
            'page',
            $page
        );
        DB::statement("SET SQL_MODE='only_full_group_by'");

        $itemIds = $relatedResults->pluck('id')->toArray();
        $data = $relatedResults->toArray();

        $paginator = $paginator = $this->getPaginatorArray($relatedResults);

        return [
            'model' => $item,
            $relationsResultsName => $relatedResults,
            'results_json' => json_encode($this->formatRelationResults($data['data'])),
            'params' => $queryData['searchParams'],
            'sort' => $queryData['sortParams'],
            'filter' => $queryData['filterData'],
            'items_count' => $itemsCount,
            'items_ids' => implode(',',$itemIds),
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
        string $selectedIds = '',
        string $selIdsKey = 'id'): array
    {
        $filterData = $this->getFilterParams($query, $filters);
        $searchParams = $this->getSearchParams($query, $searches, 'relation');
        $sortParams = $this->getSortParams($query, $sorts);
        $advParams = $this->getAdvParams($query, $sorts, 'relation');

        $this->extraRelationConditions($query);

        if (strlen(trim($selectedIds)) > 0) {
            $ids = explode('|', $selectedIds);
            $query->whereIn($selIdsKey, $ids);
        }

        return [
            'query' => $query,
            'searchParams' => $searchParams,
            'sortParams' => $sortParams,
            'filterData' => $filterData
        ];
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
        return [
            'op' => $ops[$op],
            'val' => $v
        ];
    }

    private function getAdvParams($query, array $advSearches, string $searchType = 'index'): array
    {
        $map = $searchType == 'index' ? $this->searchesMap : $this->relSearchesMap;
        $searchParams = [];
        foreach ($advSearches as $search) {
            $data = explode('::', $search);
            $key = $map[$data[0]] ?? $data[0];
            $op = $this->getSearchOperator($data[1], $data[2]);
            $query->where($key, $op['op'], $op['val']);
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

    private function getSortParams($query, array $sorts): array
    {
        $sortParams = [];
        foreach ($sorts as $sort) {
            $data = explode('::', $sort);
            $query->orderBy($data[0], $data[1]);
            $sortParams[$data[0]] = $data[1];
        }
        return $sortParams;
    }

    private function getFilterParams($query, array $filters): array
    {
        // $query->with('roles');
        $filterData = [];
        foreach ($filters as $filter) {
            $data = explode('::', $filter);
            // if ($data[0] == 'roles') {
            //     $query->withRoles([$data[1]]);
            // }
            $filterData[$data[0]]['selected'] = $data[1];
        }
        // $filterData['roles']['options'] = Role::all();

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
        return $results;
    }

    protected function formatRelationResults(array $results): array
    {
        return $results;
    }
}
?>