<?php

namespace Database\Factories;

use App\Models\ClientFamily;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'rm_id' => User::all()->random()->id,
            'client_code' => $this->faker->word(),
            'unique_code' => '',
            'name' => $this->faker->name(),
            'fresh_fund' => rand(1000000, 10000000),
            're_invest' => rand(1000000, 10000000),
            'withdrawal' => rand(1000000, 10000000),
            'payout' => rand(1000000, 10000000),
            'total_aum' => rand(1000000, 10000000),
            'other_funds' => rand(1000000, 10000000),
            'brokerage' => rand(1000000, 10000000),
            'realised_pnl' => rand(100000, 1000000),
            'pfo_type' => '',
            'category' => '',
            'type' => '',
            'fno' => '',
            'entry_date' => '',
            'pan_number' => '',
            'email' => '',
            'phone_number' => '',
            'whatsapp' => '',
            'family_id' => ClientFamily::all()->random()->id,
        ];
    }
}
