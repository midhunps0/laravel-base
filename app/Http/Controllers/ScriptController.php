<?php

namespace App\Http\Controllers;

use App\Models\Script;
use Illuminate\Http\Request;
use App\Services\ScriptService;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\SmartController;
use App\ImportExports\DefaultCollectionExports;

class ScriptController extends SmartController
{
    public function index(ScriptService $scriptService)
    {
        $data = $scriptService->index(
            $this->request->input('items_count', 10),
            $this->request->input('page'),
            $this->request->input('search', []),
            $this->request->input('sort', []),
            $this->request->input('filter', []),
            $this->request->input('selected_ids', ''),
            'clients'
        );

        return $this->getView('admin.clients.index', $data);
    }

    public function queryIds(ScriptService $userService)
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

    public function download(ScriptService $scriptService)
    {
        $clients = $scriptService->processDownload(
            $this->request->input('search', []),
            $this->request->input('sort', []),
            $this->request->input('filter', []),
            $this->request->input('selected_ids', '')
        );

        return Excel::download(new DefaultCollectionExports($clients), 'clients.xlsx');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, ScriptService $scriptService)
    {
        if ($id == 0) {
            return $this->getView('admin.clients.search');
        }
        $data = $scriptService->getShowData(
            $id,
            $this->request->input('items_count', 10),
            $this->request->input('page'),
            $this->request->input('search', []),
            $this->request->input('sort', []),
            $this->request->input('filter', []),
            $this->request->input('selected_ids', ''),
            'client_scripts'
        );

        return $this->getView('admin.clients.show', $data);
    }

    public function list( ScriptService $scriptService)
    {
        $data = $scriptService->getList($this->request->input('search'));

        return response()->json([
            'data' => $data
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
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Script  $script
     * @return \Illuminate\Http\Response
     */
    public function edit(Script $script)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Script  $script
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Script $script)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Script  $script
     * @return \Illuminate\Http\Response
     */
    public function destroy(Script $script)
    {
        //
    }
}
