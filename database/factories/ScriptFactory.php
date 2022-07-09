<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Helpers\GeneralHelper;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Script>
 */
class ScriptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $name = $this->faker->words(2, true);
        $cmp = rand(50000, 200000)/100;
        $ldc = GeneralHelper::randPcChange($cmp, 5);
        $dh = GeneralHelper::randPcChange($cmp, 2, 1);
        $dl = GeneralHelper::randPcChange($cmp, 2, -1);
        return [
            'isin_code' => Str::upper(substr(str_replace(' ', '', $name), 0, 3)),
            'symbol' => $name,
            'tracked' => true,
            'company_name' => $this->faker->words(2, true),
            'cmp' => $cmp,
            'last_day_closing' => $ldc,
            'day_high' => $dh,
            'day_low' => $dl,
            'industry' => $this->faker->word(),
            'series' => $this->faker->word(),
            'fno' => true,
            'nifty' => false,
            'nse_code' => rand(12345678, 99999999999),
            'bse_code' => rand(12345678, 99999999999),
            'yahoo_code' => Str::upper(str_replace(' ', '', $name)),
            'doc' => '',
            'bbg_ticker' => '',
            'bse_security_id' => '',
            'capitaline_code' => '',
            'mvg_sector' => '',
            'agio_indutry' => '',
            'remarks' => '',
        ];
    }
}
