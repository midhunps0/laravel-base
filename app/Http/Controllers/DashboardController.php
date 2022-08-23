<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends SmartController
{
    public function dashboard()
    {
        return $this->buildResponse('admin.dashboard');
    }

    public function masterData()
    {
        return $this->buildResponse('admin.masters.index');
    }

}
