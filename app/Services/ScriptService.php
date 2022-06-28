<?php
namespace App\Services;

use App\Models\Role;
use App\Models\User;

class ScriptService
{
    public function index(
        $itemsCount,
        $page,
        $searches,
        $sorts,
        $filters,
        $selectedIds
    ){
        $queryData = $this->getQueryAndParams(
            $searches,
            $sorts,
            $filters,
            $selectedIds
        );
        $users = $queryData['query']->paginate(
            $itemsCount,
            ['*'],
            'page',
            $page
        );

        $itemIds = $users->pluck('id')->toArray();
        $data = $users->toArray();

        return [
            'users' => $users,
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
        $searches,
        $sorts,
        $filters,
        $selectedIds
    ){
        $queryData = $this->getQueryAndParams(
            $searches,
            $sorts,
            $filters,
            $selectedIds
        );
        $users = $queryData['query']->get();
        return $users;
    }

    public function getIdsForParams(
        $searches,
        $sorts,
        $filters
    ){
        $queryData = $this->getQueryAndParams(
            $searches,
            $sorts,
            $filters
        );

        $users = $queryData['query']->get()->pluck('id');
        return $users;
    }

    private function getQueryAndParams(
        $searches,
        $sorts,
        $filters,
        $selectedIds = ''
    ) {
        $searchParams = [];
        $sortParams = [];
        $filterData = [];

        $query = User::with('roles');

        foreach ($searches as $search) {
            $data = explode('::', $search);
            $query->where($data[0], 'like', '%'.$data[1].'%');
            $searchParams[$data[0]] = $data[1];
        }
        foreach ($sorts as $sort) {
            $data = explode('::', $sort);
            $query->orderBy($data[0], $data[1]);
            $sortParams[$data[0]] = $data[1];
        }
        foreach ($filters as $filter) {
            $data = explode('::', $filter);
            if ($data[0] == 'roles') {
                $query->withRoles([$data[1]]);
            }
            $filterData[$data[0]]['selected'] = $data[1];
        }
        $filterData['roles']['options'] = Role::all();

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
}
?>