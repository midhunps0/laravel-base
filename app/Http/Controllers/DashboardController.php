<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends SmartController
{
    public function dashboard()
    {
        return $this->getView('admin.dashboard');
    }
}
