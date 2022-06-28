<?php
namespace App\Services;

use Illuminate\Database\Eloquent\Collection;

trait IsModelViewConnector{
    protected $query;
    protected $itemQuery;
    protected $selects = '*';

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function index(
        int $itemsCount,
        ?int $page,
        array $searches,
        array $sorts,
        array $filters,
        string $selectedIds,
        string $resultsName = 'results'
    ): array {
        $queryData = $this->getQueryAndParams(
            $searches,
            $sorts,
            $filters,
            $selectedIds
        );

        $results = $queryData['query']->paginate(
            $itemsCount,
            $this->selects,
            'page',
            $page
        );
        $itemIds = $results->pluck('id')->toArray();
        $data = $results->toArray();

        return [
            $resultsName => $results,
            'params' => $queryData['searchParams'],
            'sort' => $queryData['sortParams'],
            'filter' => $queryData['filterData'],
            'items_count' => $itemsCount,
            'items_ids' => implode(',',$itemIds),
            'total_results' => $data['total'],
            'current_page' => $data['current_page']
        ];
    }

    public function processDownload(
        array $searches,
        array $sorts,
        array $filters,
        string $selectedIds
    ): Collection {
        $queryData = $this->getQueryAndParams(
            $searches,
            $sorts,
            $filters,
            $selectedIds
        );
        $results = $queryData['query']->get();
        return $results;
    }

    public function getIdsForParams(
        array $searches,
        array $sorts,
        array $filters
    ): array {
        $queryData = $this->getQueryAndParams(
            $searches,
            $sorts,
            $filters
        );

        $results = $queryData['query']->get()->pluck('id');
        return $results;
    }

    public function getQueryAndParams(
        array $searches,
        array $sorts,
        array $filters,
        string $selectedIds = ''
    ): array {
        $filterData = $this->getFilterParams($this->query, $filters);
        $searchParams = $this->getSearchParams($this->query, $searches);
        $sortParams = $this->getSortParams($this->query, $sorts);

        if (strlen(trim($selectedIds)) > 0) {
            $ids = explode('|', $selectedIds);
            $this->query->whereIn('id', $ids);
        }

        // if ($this->selects != '*') {
        //     $this->query->select($this->selects);
        // }

        return [
            'query' => $this->query,
            'searchParams' => $searchParams,
            'sortParams' => $sortParams,
            'filterData' => $filterData
        ];
    }

    public function getItem(string $arg): Collection
    {
        return $this->itemQuery->find($arg);
    }

    private function getSearchParams($query, array $searches): array
    {
        $searchParams = [];
        foreach ($searches as $search) {
            $data = explode('::', $search);
            $query->where($data[0], 'like', '%'.$data[1].'%');
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
}
?>