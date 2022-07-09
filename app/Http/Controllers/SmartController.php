<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SmartController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function buildResponse($view, $args = [])
    {
        if ($this->request->header('X-ACCEPT-MODE') == 'only-json') {
            return response()->json($args);
        }
        $args['x_ajax'] = $this->request->input('x_mode') == 'ajax';
        if ($args['x_ajax']) {
            return view($view)->with($args)->render();
        }
        return view($view)->with($args);
    }
}
