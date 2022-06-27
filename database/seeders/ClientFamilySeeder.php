<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use App\Models\ClientFamily;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ClientFamilySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 1; $i<123; $i++) {
            ClientFamily::create([
                'code' => 'F'.Str::padLeft($i, 3, '0')
            ]);
        }
    }
}
