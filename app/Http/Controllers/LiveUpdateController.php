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
        $scriptcode = "ScripCode";
        info('LTPs count: ' . count($ltps));
        info('OHLCs count: ' . count($ohlcs));
        try {
            foreach ($ltps as $ltp) {
                switch($ltp['Exchange']) {
                    case 'NSE':
                        $script = Script::where('nse_code', $ltp[$scriptcode])->get()->first();
                        break;
                    case 'BSE':
                        $script = Script::where('bse_code', $ltp[$scriptcode])->get()->first();
                        break;
                    default:
                        break;
                }
                if (!isset($script)) {
                    continue;
                }
                $script->cmp = $ltp['LTP_Rate'];
                $script->save();
            }

            foreach ($ohlcs as $ohlc) {
                switch($ohlc['Exchange']) {
                    case 'NSE':
                        $script = Script::where('nse_code', $ohlc[$scriptcode])->get()->first();
                        break;
                    case 'BSE':
                        $script = Script::where('bse_code', $ohlc[$scriptcode])->get()->first();
                        break;
                    default:
                        break;
                }
                if (!isset($script)) {
                    continue;
                }
                $script->day_high = $ohlc['High'];
                $script->day_low = $ohlc['Low'];
                $script->last_day_closing = $ohlc['PrevDayClose'];
                $script->save();
            }

            return response('Ok', 200);
        } catch (Exception $e) {
            return response('Invalid input.', 400);
        }
    }
}
