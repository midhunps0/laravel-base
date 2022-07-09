<?php
namespace App\Helpers;

use App\Models\Script;

class AppHelper
{
    public static function getLiquidbees()
    {
        $lbs = config('appSettings.liquidbees') ?? [];
        return Script::whereIn('symbol', $lbs)->pluck('id')->toArray();
    }
}
?>