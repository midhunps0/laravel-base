<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    private $permissionsStrings = [
        'user',
        'dealer',
        'customer',
        'script',
        'role',
        'permission'
    ];
    private $permissionActions = [
        'view_any',
        'create_any',
        'edit_any',
        'delete_any',
        'view_own',
        'create_own',
        'edit_own',
        'delete_own',
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = $this->generatePermissions();
        foreach ($permissions as $p) {
            Permission::create(
                [
                    'name' => $p
                ]
            );
        }
    }

    private function generatePermissions()
    {
        $permissions = [];
        foreach ($this->permissionsStrings as $p) {
            foreach ($this->permissionActions as $a) {
                if (!($p == 'user' && in_array($a, [
                    'view_own',
                    'create_own',
                    'edit_own',
                    'delete_own'
                ]))) {
                    $permissions[] = sprintf('%s.%s', $p, $a);
                }
            }
        }
        return $permissions;
    }
}
