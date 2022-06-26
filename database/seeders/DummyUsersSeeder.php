<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DummyUsersSeeder extends Seeder
{
    private $roles = ['admin', 'dealer'];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::factory()->count(30)->create();
        foreach ($users as $user) {
            $user->assignRole($this->roles[rand(0,1)]);
        }
    }
}
