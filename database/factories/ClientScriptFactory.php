<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Script;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClientScript>
 */
class ClientScriptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'client_id' => Client::all()->random()->id,
            'script_id' => Script::all()->random()->id,
            'entry_date' => $this->faker->date(),
            'db_qty' => rand(100, 1000),
            'available_qty' => rand(100, 1000),
            'buy_avg_price' => rand(500, 4000)/100
        ];
    }
}
