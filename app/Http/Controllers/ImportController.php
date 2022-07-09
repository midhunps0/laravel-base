<?php

namespace App\Http\Controllers;

use App\Services\ClientService;
use Maatwebsite\Excel\Facades\Excel;
use App\ImportExports\TradeBackupImport;

class ImportController extends SmartController
{
    public function tradeBackupForm()
    {
        return $this->buildResponse('admin.masters.import-tradebackup');
    }

    public function tradeBackupImport()
    {
        try {
            $tbi = new TradeBackupImport();
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
}
