<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class UserController extends SmartController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $itemsCount = $this->request->input('items_count', 10);
        $page = $this->request->input('page');
        $searches = $this->request->input('search', []);
        $searchParams = [];
        $sorts = $this->request->input('sort', []);
        $sortParams = [];
        $filters = $this->request->input('filter', []);
        $filterData = [];
        $selectedIds = $this->request->input('selected_ids', []);

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
        $users = $query->paginate(
            $itemsCount,
            ['*'],
            'page',
            $page
        );

        $filterData['roles']['options'] = Role::all();
        $itemIds = $users->pluck('id')->toArray();
        $data = $users->toArray();

        return $this->ajaxView('admin.users.index', [
            'users' => $users,
            'params' => $searchParams,
            'sort' => $sortParams,
            'filter' => $filterData,
            'items_count' => $itemsCount,
            'items_ids' => implode(',',$itemIds),
            'total_results' => $data['total'],
            'current_page' => $data['current_page']
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    // private function getUniqueParams(Collection $results, $param, $key)
    // {
    //     $ar = [];
    //     $ids = [];
    //     foreach ($results as $result) {
    //         if (!in_array($result->$param->$key, $ids)) {
    //             $ar = array_push($ar, $result->$param);
    //             $ids = array_push($ids, $resul->$param->$key);
    //         }
    //     }
    //     return $ar;
    // }
}
