<?php

namespace App\Http\Controllers;

use App\Services\ClientScriptService;
use Maatwebsite\Excel\Facades\Excel;
use App\ImportExports\DefaultArrayExports;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class ClientScriptController extends SmartController
{
    public function index(ClientScriptService $ClientScriptService)
    {
        $data = $ClientScriptService->index(
            $this->request->input('items_count', 10),
            $this->request->input('page'),
            $this->request->input('search', []),
            $this->request->input('sort', []),
            $this->request->input('filter', []),
            $this->request->input('adv_search', []),
            $this->request->input('selected_ids', ''),
            'clientscripts'
        );

        return $this->buildResponse('admin.clientscripts.index', $data);
    }

    public function queryIds(ClientScriptService $clientScriptService)
    {
        $ids = $clientScriptService->getIdsForParams(
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

    public function download(ClientScriptService $ClientScriptService)
    {
        $clients = $ClientScriptService->processDownload(
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

    public function list(ClientScriptService $ClientScriptService)
    {
        $data = $ClientScriptService->getList($this->request->input('search'));

        return response()->json([
            'data' => $data
        ]);
    }

    // public function queryShowIds(ClientScriptService $ClientScriptService)
    // {
    //     $ids = $ClientScriptService->getShowIdsForParams(
    //         $this->request->input('search', []),
    //         $this->request->input('sort', []),
    //         $this->request->input('filter', []),
    //         $this->request->input('adv_search', []),
    //     );

    //     return response()->json([
    //         'success' => true,
    //         'ids' => $ids
    //     ]);
    // }

    public function downloadOrder($id, ClientScriptService $ClientScriptService)
    {
        $order = $ClientScriptService->downloadOrder(
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
        return Excel::download(new DefaultArrayExports($order, $colsFormat), 'order.xlsx');
    }
}
