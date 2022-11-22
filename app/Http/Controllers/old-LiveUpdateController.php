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
		$savedLtps = [];

		info('ltps: '.count($ltps).' OHLCs: '.count($ohlcs));

        $allItems = [];
        foreach ($ltps as $ltp) {
            $allItems[$ltp[$scriptcode]]['ltp'] = $ltp;
        }
        foreach ($ohlcs as $ohlc) {
            $allItems[$ohlc[$scriptcode]]['ohlc'] = $ohlc;
        }
		$count = 0;
        try {
            foreach ($allItems as $code => $item) {
                $ltp = $item['ltp'];
                $ohlc = $item['ohlc'];
                switch($ltp['Exchange']) {
                    case 'NSE':
                        Script::where('nse_code', $code)->update(
                            [
                                'cmp' => $ltp['LTP_Rate'],
                                'day_high' => $ohlc['High'],
                                'day_low' => $ohlc['Low'],
                                'last_day_closing' => $ohlc['PrevDayClose'],
                            ]
                        );
                        break;
                    case 'BSE':
                        Script::where('bse_code', $code)->update(
                            [
                                'cmp' => $ltp['LTP_Rate'],
                                'day_high' => $ohlc['High'],
                                'day_low' => $ohlc['Low'],
                                'last_day_closing' => $ohlc['PrevDayClose'],
                            ]
                        );
                        break;
                    default:
                        break;
                }
				if (!isset($script)) {
                    continue;
                }
				$count++;
            }
			info('LTP/OHLCss saved: '.$count);
            return response('Ok', 200);
        } catch (Exception $e) {
            return response('Invalid input.', 400);
        }
    }
}
