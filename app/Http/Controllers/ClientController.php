<?php

namespace App\Http\Controllers;

use App\Services\ClientService;
use Maatwebsite\Excel\Facades\Excel;
use App\ImportExports\DefaultArrayExports;
use App\ImportExports\DefaultCollectionExports;
use Symfony\Component\Finder\Exception\AccessDeniedException;

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
            $this->request->input('adv_search', []),
            $this->request->input('selected_ids', ''),
            'clients'
        );

        return $this->buildResponse('admin.clients.index', $data);
    }

    public function queryIds(ClientService $userService)
    {
        $ids = $userService->getIdsForParams(
            $this->request->input('search', []),
            $this->request->input('sort', []),
            $this->request->input('filter', []),
            $this->request->input('adv_search', [])
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
            $this->request->input('adv_search', []),
            $this->request->input('selected_ids', '')
        );
        $colsFormat = [
            'client_code',
            'name',
            'total_aum',
            'allocated_aum',
            'cur_value',
            'pnl',
            'pnl_pc',
            'realised_pnl',
            'liquidbees',
            'cash',
            'cash_pc',
            'returns',
            'returns_pc',
            'rm'
        ];
        return Excel::download(new DefaultArrayExports($clients, $colsFormat), 'clients.xlsx');
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
            return $this->buildResponse('admin.clients.search');
        }
        try {
            $data = $clientService->getShowData(
                $id,
                $this->request->input('items_count', 10),
                $this->request->input('page'),
                $this->request->input('search', []),
                $this->request->input('sort', []),
                $this->request->input('filter', []),
                $this->request->input('adv_search', []),
                $this->request->input('selected_ids', ''),
                'scripts'
            );
        } catch (AccessDeniedException $e) {
            return $this->buildResponse('admin.clients.search', [
                'error' => $e->__toString(),
                'error_type' => 'access denied'
            ]);
        } catch (\Throwable $e) {
            return $this->buildResponse('admin.clients.search', [
                'error' => $e->__toString(),
                'error_type' => 'unknown'
            ]);
        }

        return $this->buildResponse('admin.clients.show', $data);
    }

    public function list(ClientService $clientService)
    {
        $data = $clientService->getList($this->request->input('search'));

        return response()->json([
            'data' => $data
        ]);
    }

    public function queryShowIds($id, ClientService $clientService)
    {
        $ids = $clientService->getShowIdsForParams(
            $id,
            $this->request->input('search', []),
            $this->request->input('sort', []),
            $this->request->input('filter', []),
            $this->request->input('adv_search', []),
        );

        return response()->json([
            'success' => true,
            'ids' => $ids
        ]);
    }

    public function showDownload($id, ClientService $clientService)
    {
        $clients = $clientService->processShowDownload(
            $id,
            $this->request->input('search', []),
            $this->request->input('sort', []),
            $this->request->input('filter', []),
            $this->request->input('adv_search', []),
            $this->request->input('selected_ids', '')
        );
        $colsFormat = [
            'symbol',
            'entry_date',
            'pa',
            'category',
            'sector',
            'qty',
            'buy_avg_price',
            'amt_invested',
            'cmp',
            'cur_value',
            'overall_gain',
            'pc_change',
            'todays_gain',
            'day_high',
            'day_low',
            'impact',
            'nof_days'
        ];
        return Excel::download(new DefaultArrayExports($clients, $colsFormat), 'clients.xlsx');
    }
}
