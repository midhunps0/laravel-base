<?php

namespace App\Http\Controllers;

use App\ImportExports\UserExports;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends SmartController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UserService $userService)
    {
        $data = $userService->index(
            $this->request->input('items_count', 10),
            $this->request->input('page'),
            $this->request->input('search', []),
            $this->request->input('sort', []),
            $this->request->input('filter', []),
            $this->request->input('selected_ids', '')
        );

        return $this->getView('admin.users.index', $data);
    }

    public function queryIds(UserService $userService)
    {
        $ids = $userService->getIdsForParams(
            $this->request->input('search', []),
            $this->request->input('sort', []),
            $this->request->input('filter', [])
        );

        return response()->json([
            'success' => true,
            'ids' => $ids
        ]);
    }

    public function download(UserService $userService)
    {
        $users = $userService->processDownload(
            $this->request->input('search', []),
            $this->request->input('sort', []),
            $this->request->input('filter', []),
            $this->request->input('selected_ids', '')
        );
        return Excel::download(new UserExports($users), 'invoices.xlsx');
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
