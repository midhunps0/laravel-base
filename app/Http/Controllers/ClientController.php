<?php

namespace App\Http\Controllers;

use App\ImportExports\DefaultCollectionExports;
use App\Services\ClientService;
use Maatwebsite\Excel\Facades\Excel;

class ClientController extends SmartController
{
    public function index(ClientService $clientService)
    {
        $data = $clientService->index(
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

    public function queryIds(ClientService $userService)
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

    public function download(ClientService $clientService)
    {
        $clients = $clientService->processDownload(
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
    public function show($id, ClientService $clientService)
    {
        if ($id == 0) {
            return $this->getView('admin.clients.search');
        }
        $data = $clientService->getShowData(
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

    public function list( ClientService $clientService)
    {
        $data = $clientService->getList($this->request->input('search'));

        return response()->json([
            'data' => $data
        ]);
    }

}
