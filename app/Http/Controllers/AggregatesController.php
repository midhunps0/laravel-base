<?php

namespace App\Http\Controllers;

use App\Http\Controllers\SmartController;
use App\Services\AggregatesService;

class AggregatesController  extends SmartController
{
    public function adminIndex(AggregatesService $aggregatesService)
    {
        $data = $aggregatesService->adminIndex(
            $this->request->input('items_count', 100),
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
}
