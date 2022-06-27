<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    private $teamLeaders = [
        [
            'name' => 'Remya mohan',
            'email' => 'remya@agio.in',
            'username' => 'Remya',
            'dealers' => [
                [
                    'name' => 'Praveena',
                    'email' => 'praveena@agio.in',
                    'username' => 'Praveena',

                ],
                [
                    'name' => 'Sreejith P S',
                    'email' => 'sreejith@agio.in',
                    'username' => 'Sreejith',
                ],
                [
                    'name' => 'Aswathi Arjun',
                    'email' => 'aswathy@agio.in',
                    'username' => 'Aswathi',
                ],
                [
                    'name' => 'Renthik',
                    'email' => 'trainee1@agio.in',
                    'username' => 'Renthik',
                ],
            ]
        ],
        [
            'name' => 'Seena biju',
            'email' => 'seena@agio.in',
            'username' => 'Seena',
            'dealers' => [
                [
                    'name' => 'Rajitha M R',
                    'email' => 'rajitha@agio.in',
                    'username' => 'Rajitha',
                ],
                [
                    'name' => 'Aneeshya antony',
                    'email' => 'aneeshya@agio.in',
                    'username' => 'Aneeshya',
                ],
                [
                    'name' => 'Saji T A',
                    'email' => 'saji@agio.in',
                    'username' => 'Saji',
                ],
                [
                    'name' => 'Shinorans',
                    'email' => 'trainee2@agio.in',
                    'username' => 'Shino',
                ],
                [
                    'name' => 'Jobin O K',
                    'email' => 'jobin@agio.in',
                    'username' => 'Jobin',
                ],
            ]
        ],
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'superadmin@demo.com',
            'password' => Hash::make('abcd1234'),
        ]);
        $superAdmin->assignRole('Super Admin');

        $admin = User::factory()->create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@demo.com',
            'password' => Hash::make('abcd1234'),
        ]);
        $admin->assignRole('Admin');

        foreach ($this->teamLeaders as $tl) {
            $u = User::factory()->create([
                'name' => $tl['name'],
                'username' => $tl['username'],
                'email' => $tl['email'],
                'password' => Hash::make(
                    substr(
                        Str::lower(
                            str_replace('.', '', str_replace(' ', '', $tl['username']))
                        ),
                        0,
                        4
                    ).'1234'),
            ]);
            $u->assignRole('Team Leader');
            foreach ($tl['dealers'] as $dl) {
                $d = User::factory()->create([
                    'name' => $dl['name'],
                    'username' => $dl['username'],
                    'email' => $dl['email'],
                    'password' => Hash::make(
                        substr(
                            Str::lower(
                                str_replace('.', '', str_replace(' ', '', $tl['username']))
                            ),
                            0,
                            4
                        ).'1234'
                    ),
                ]);
                $d->assignRole('Dealer');
            }
        }
    }
}
