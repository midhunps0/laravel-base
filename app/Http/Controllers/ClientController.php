<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientScriptUpdtateRequest;
use App\Models\User;
use App\Models\Client;
use App\Services\ClientService;
use App\ImportExports\ClientsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\ImportExports\DefaultArrayExports;
use App\Http\Requests\ClientsImportRequest;
use App\Http\Requests\ClientUpdateRequest;
use App\ImportExports\DefaultCollectionExports;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class ClientController extends SmartController
{
    public function index(ClientService $clientService)
    {
        $data = $clientService->index(
            $this->request->input('items_count', 30),
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
        // dd($clients);
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
            'dealer'
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
                $this->request->input('items_count', 30),
                $this->request->input('page'),
                $this->request->input('search', []),
                $this->request->input('sort', []),
                $this->request->input('filter', ['tracked::1']),
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
        return Excel::download(new DefaultArrayExports($clients, $colsFormat), 'client_scripts.xlsx');
    }

    public function downloadOrder($id, ClientService $clientService)
    {
        $order = $clientService->downloadOrder(
            $id,
            $this->request->input('search', []),
            $this->request->input('sort', []),
            $this->request->input('filter', []),
            $this->request->input('adv_search', []),
            $this->request->input('selected_ids', ''),
            $this->request->input('qty'),
            $this->request->input('price', null),
            $this->request->input('slippage'),
        );

        $colsFormat = [
            'exchange_code',
            'buyorsell',
            'product',
            'script_name',
            'qty',
            'lot',
            'order_type',
            'price',
            'client_code',
            'discount_qty',
            'trigger_price',
        ];

        $colsTitles = [
            'ExchangeCode',
            'BuyOrSell',
            'Product',
            'ScripName',
            'Qty',
            'Lot',
            'OrderType',
            'Price',
            'ClientCode',
            'DiscQty',
            'TriggerPrice',
        ];

        return Excel::download(new DefaultArrayExports($order, $colsFormat, $colsTitles), 'clientsellorder.csv');
    }

    public function bulkImportCreate()
    {
        return $this->buildResponse('admin.clients.import');
    }

    public function bulkImportStore(ClientsImportRequest $request, ClientService $clientService)
    {
        try {
            $tbi = new ClientsImport();
            Excel::import($tbi, $this->request->file('file'));

            return response()->json([
                'success' => true,
                'total_items' => $tbi->totalItems,
                'failed_items' => $tbi->failedItems
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->__toString()
            ]);
        }
    }

    public function create()
    {
        return $this->buildResponse('admin.clients.create');
    }

    public function edit(Client $client)
    {
        $dealers = User::withRoles(['Dealer'])->get();
        $pfo_types = Config('appSettings.client_pfo_types');
        $categories = Config('appSettings.client_categories');
        $types = Config('appSettings.client_types');
        return $this->buildResponse('admin.clients.edit',
            [
                'client' => $client,
                'dealers' => $dealers,
                'pfo_types' => $pfo_types,
                'categories' => $categories,
                'types' => $types
            ]
        );
    }

    public function update(ClientUpdateRequest $request, $client, ClientService $clientService)
    {
        $result = $clientService->update($client, $request->validated());
        return response()->json([
            'success' => $result,
        ]);
    }

    public function updateScript(ClientScriptUpdtateRequest $request, $id, ClientService $clientService)
    {
        $result = $clientService->updateScript($id, $request->validated());
        return response()->json([
            'success' => true,
        ]);
    }
}
