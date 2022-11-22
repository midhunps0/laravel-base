<?php

namespace App\Http\Controllers;

use App\Models\Script;
use Exception;
use Illuminate\Http\Request;

class LiveUpdateController extends Controller
{
    public function liveUpdate(Request $request)
    {
		$allItems = $request->input('payload', []);

		$count = 0;
        try {
            foreach ($allItems as $item) {
                $r = Script::where(
                    'instrument_token',
                    $item['instrument_token']
                )->update(
                    [
                        'cmp' => $item['ltp'],
                        'day_high' => $item['high'],
                        'day_low' => $item['low'],
                        'last_day_closing' => $item['close'],
                    ]
                );
                if ($r > 0) {
                    $count++;
                }
            }

            return response('Ok', 200);
        } catch (Exception $e) {
            return response('Invalid input.', 400);
        }
    }
}
