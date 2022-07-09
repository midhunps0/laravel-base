<?php
namespace App\Helpers;

class GeneralHelper
{
    public static function randomPercentage($min, $max) {
        return rand(round($max*100), round($min*100))/100;
    }

    public static function randPcChange($val, $pc, $dir = null)
    {
        $chance = [-1, 1];
        $dir = isset($dir) ? $dir : $chance[rand(0,1)];
        $pc = Self::randomPercentage(0, $pc);
        $change = $dir * $val * $pc / 100;
        $val += $change;
        return $val;
    }
}
?>