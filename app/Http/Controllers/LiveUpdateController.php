<?php

namespace App\Http\Controllers;

use App\Models\Script;
use Exception;
use Illuminate\Http\Request;

class LiveUpdateController extends Controller
{
    public function liveUpdate(Request $request)
    {
        $ltps = $request->input('LTP', []);
        $ohlcs = $request->input('OHLC', []);
        $scriptcode = "Scrip Code";
        try {
            foreach ($ltps as $ltp) {
                $script = Script::where('nse_code', $ltp->$scriptcode)->get()->first();
                if (!isset($script)) {
                    continue;
                }
                $script->cmp = $ltp->LTP_Rate;
                $script->save();
            }

            foreach ($ohlcs as $ohlc) {
                $script = Script::where('nse_code', $ohlc->$scriptcode)->get()->first();
                if (!isset($script)) {
                    continue;
                }
                $script->day_high = $ohlc->High;
                $script->day_low = $ohlc->Low;
                $script->last_day_closing = $ohlc->PrevDayClose;
                $script->save();
            }

            return response('Ok', 200);
        } catch (Exception $e) {
            return response('Invalid input.', 400);
        }
    }
}
