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

    public function verifySellOrder(ClientScriptService $clientScriptService)
    {
        $valid = $clientScriptService->verifySellOrder(
            $this->request->input('search', []),
            $this->request->input('sort', []),
            $this->request->input('filter', []),
            $this->request->input('adv_search', []),
            $this->request->input('selected_ids', ''));

            return response()->json([
                'success' => $valid,
                'message' => $valid ? 'The list validated successfully. You can now generate the order.' : 'List validation failed. Some items don\'t have a symbol.'
            ]);
    }

    public function analyseSellOrder(ClientScriptService $clientScriptService)
    {
        $analysis = $clientScriptService->analyseSellOrder(
            $this->request->input('search', []),
            $this->request->input('sort', []),
            $this->request->input('filter', []),
            $this->request->input('adv_search', []),
            $this->request->input('selected_ids', ''));

            return response()->json([
                'uniqueSymbol' => $analysis['unique'],
                'symbol' => $analysis['symbol'],
                'price' => $analysis['price'],
            ]);
    }

    public function downloadOrder(ClientScriptService $ClientScriptService)
    {
        $order = $ClientScriptService->downloadOrder(
            $this->request->input('search', []),
            $this->request->input('sort', []),
            $this->request->input('filter', []),
            $this->request->input('adv_search', []),
            $this->request->input('selected_ids', ''),
            $this->request->input('qty'),
            $this->request->input('price'),
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

        return Excel::download(new DefaultArrayExports($order, $colsFormat, $colsTitles), 'sellorder.csv');
    }
}
