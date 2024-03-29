<?php

namespace App\Http\Controllers;

use App\Models\Script;
use Illuminate\Http\Request;
use App\Services\ScriptService;
use App\ImportExports\ScriptsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\SmartController;
use App\Http\Requests\ScriptUpdateRequest;
use App\ImportExports\DefaultArrayExports;
use App\Http\Requests\ScriptsImportRequest;
use App\ImportExports\DefaultCollectionExports;

class ScriptController extends SmartController
{
    public function index(ScriptService $scriptService)
    {
        $data = $scriptService->index(
            $this->request->input('items_count', 100),
            $this->request->input('page'),
            $this->request->input('search', []),
            $this->request->input('sort', []),
            $this->request->input('filter', []),
            $this->request->input('adv_search', []),
            $this->request->input('selected_ids', ''),
            'scripts'
        );

        return $this->buildResponse('admin.scripts.index', $data);
    }

    public function queryIds(ScriptService $scriptService)
    {
        $ids = $scriptService->getIdsForParams(
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

    public function download(ScriptService $scriptService)
    {
        $scripts = $scriptService->processDownload(
            $this->request->input('search', []),
            $this->request->input('sort', []),
            $this->request->input('filter', []),
            $this->request->input('adv_search', []),
            $this->request->input('selected_ids', '')
        );
        $colsFormat = [
            'dealer',
            'symbol',
            'pa',
            'sector',
            'tot_qty',
            'abv',
            'amt_invested',
            'cmp',
            'cur_value',
            'overall_gain',
            'gain_pc',
            'todays_gain',
            'day_high',
            'day_low',
            'impact'
        ];
        return Excel::download(new DefaultArrayExports($scripts, $colsFormat), 'scripts.xlsx');
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
            return $this->buildResponse('admin.scripts.search');
        }
        $data = $scriptService->getShowData(
            $id,
            $this->request->input('items_count', 100),
            $this->request->input('page'),
            $this->request->input('search', []),
            $this->request->input('sort', []),
            $this->request->input('filter', []),
            $this->request->input('adv_search', []),
            $this->request->input('selected_ids', ''),
            'clients'
        );

        return $this->buildResponse('admin.scripts.show', $data);
    }

    public function list(ScriptService $scriptService)
    {
        $data = $scriptService->getList($this->request->input('search'));

        return response()->json([
            'data' => $data
        ]);
    }

    public function queryShowIds($id, ScriptService $scriptService)
    {
        $ids = $scriptService->getShowIdsForParams(
            $id,
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

    public function showDownload($id, ScriptService $scriptService)
    {
        $scripts = $scriptService->processShowDownload(
            $id,
            $this->request->input('search', []),
            $this->request->input('sort', []),
            $this->request->input('filter', []),
            $this->request->input('adv_search', []),
            $this->request->input('selected_ids', '')
        );
        $colFormat = [
            'code',
            'qty',
            'buy_avg_price',
            'buy_val',
            'cmp',
            'cur_val',
            'pnl',
            'pnl_pc',
            'nof_days',
            'impact',
            'pa'
        ];
        return Excel::download(new DefaultArrayExports($scripts, $colFormat), 'scripts.xlsx');
    }

    public function downloadOrder($id, ScriptService $ScriptService)
    {
        $order = $ScriptService->downloadOrder(
            $id,
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
        $indutries = Script::select('industry')->distinct()->get();
        $serie = Script::select('series')->distinct()->get();
        $mvg_sectors = Script::select('mvg_sector')->distinct()->get();
        $agio_industries = Script::select('agio_indutry')->distinct()->get();
        return $this->buildResponse('admin.scripts.edit', [
            'script' => $script,
            'industries' => $indutries,
            'serie' => $serie,
            'mvg_sectors' => $mvg_sectors,
            'agio_industries' => $agio_industries
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Script  $script
     * @return \Illuminate\Http\Response
     */
    public function update(ScriptUpdateRequest $request, $script, ScriptService $scriptService)
    {
        $result = $scriptService->update($script, $request->validated());
        return response()->json([
            'success' => $result,
        ]);
    }


    public function bulkImportCreate()
    {
        return $this->buildResponse('admin.scripts.import');
    }

    public function bulkImportStore(ScriptsImportRequest $request, ScriptService $clientService)
    {
        try {
            $tbi = new ScriptsImport();
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
