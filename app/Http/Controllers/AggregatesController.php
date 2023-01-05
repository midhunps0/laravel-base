<?php

namespace App\Http\Controllers;

use App\Http\Controllers\SmartController;
use App\Services\AggregatesService;

class AggregatesController  extends SmartController
{
    public function adminIndex(AggregatesService $aggregatesService)
    {
        if (auth()->user()->hasRole('Admin')) {
            $method = 'adminIndex';
            $view = 'admin.overview.admin';
        } else {
            $method = 'rmIndex';
            $view = 'admin.overview.rm';
        }
        $data = $aggregatesService->$method(
            $this->request->input('items_count', 100),
            $this->request->input('page'),
            $this->request->input('search', []),
            $this->request->input('sort', []),
            $this->request->input('filter', []),
            $this->request->input('adv_search', []),
            $this->request->input('selected_ids', ''),
            'aggr'
        );

        return $this->buildResponse($view, $data);
    }

    public function adminSelectIds(AggregatesService $aggregatesService)
    {
        $ids = $aggregatesService->adminSelectIds(
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

    public function rmIndex(AggregatesService $aggregatesService)
    {
        $data = $aggregatesService->rmIndex(
            $this->request->input('items_count', 100),
            $this->request->input('page'),
            $this->request->input('search', []),
            $this->request->input('sort', []),
            $this->request->input('filter', []),
            $this->request->input('adv_search', []),
            $this->request->input('selected_ids', ''),
            'aggr'
        );

        return $this->buildResponse('admin.overview.rm', $data);
    }

    public function rmSelectIds(AggregatesService $aggregatesService)
    {
        $ids = $aggregatesService->rmSelectIds(
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
}
