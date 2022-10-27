<?php
namespace App\Helpers;

use App\Models\Script;
use Illuminate\Support\Facades\DB;

class AppHelper
{
    public static function getLiquidbees()
    {
        $lbs = config('appSettings.liquidbees') ?? [];
        return Script::whereIn('symbol', $lbs)->pluck('id')->toArray();
    }

    public static function getDistinctCategories() {
        return DB::table('clients')->selectRaw('DISTINCT category')->pluck('category');
    }
}
?>
