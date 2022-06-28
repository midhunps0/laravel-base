<?php

namespace App\Http\Controllers;

use App\Models\ClientScript;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\ClientScriptService;
use App\ImportExports\DefaultCollectionExports;

class ClientScriptController extends SmartController
{
    public function index(ClientScriptService $clientScriptService)
    {
        $data = $clientScriptService->index(
            $this->request->input('items_count', 10),
            $this->request->input('page'),
            $this->request->input('search', []),
            $this->request->input('sort', []),
            $this->request->input('filter', []),
            $this->request->input('selected_ids', ''),
            'client_scripts'
        );

        return $this->getView('admin.clientscripts.index', $data);
    }

    public function queryIds(ClientScriptService $clientScriptService)
    {
        $ids = $clientScriptService->getIdsForParams(
            $this->request->input('search', []),
            $this->request->input('sort', []),
            $this->request->input('filter', [])
        );

        return response()->json([
            'success' => true,
            'ids' => $ids
        ]);
    }

    public function download(ClientScriptService $clientScriptService)
    {
        $clientScripts = $clientScriptService->processDownload(
            $this->request->input('search', []),
            $this->request->input('sort', []),
            $this->request->input('filter', []),
            $this->request->input('selected_ids', '')
        );

        return Excel::download(new DefaultCollectionExports($clientScripts), 'clients.xlsx');
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
     * @param  \App\Models\ClientScript  $clientScript
     * @return \Illuminate\Http\Response
     */
    public function show(ClientScript $clientScript)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ClientScript  $clientScript
     * @return \Illuminate\Http\Response
     */
    public function edit(ClientScript $clientScript)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClientScript  $clientScript
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClientScript $clientScript)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ClientScript  $clientScript
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClientScript $clientScript)
    {
        //
    }
}
