<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesPermissionsSeeder extends Seeder
{
    private $rolesPermissions = [
        'admin' => [
            'user.view_any',
            'user.create_any',
            'user.edit_any',
            'user.delete_any',
            'dealer.view_any',
            'dealer.create_any',
            'dealer.edit_any',
            'dealer.delete_any',
            'customer.view_any',
            'customer.create_any',
            'customer.edit_any',
            'customer.delete_any',
            'script.view_any',
            'script.create_any',
            'script.edit_any',
            'script.delete_any',
            'role.view_any',
            'role.create_any',
            'role.edit_any',
            'role.delete_any',
            'permission.view_any',
            'permission.create_any',
            'permission.edit_any',
            'permission.delete_any',
        ],
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->rolesPermissions as $role => $permissions) {
            foreach ($permissions as $permission) {
                $roleObject = Role::where('name', $role)->get()->first();
                $roleObject->assignPermissions([$permission]);
            }
        }
    }
}
