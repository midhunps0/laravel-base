<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Facades\Excel;

class Role extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_roles', 'role_id', 'user_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'roles_permissions',
            'role_id',
            'permission_id'
        );
    }

    public function hasPermission($permissionName)
    {
        return in_array(
            $permissionName,
            array_values($this->permissions()->pluck('name')->toArray())
        );
    }

    public function assignPermissions(array $permissionNames)
    {
        $permissionIds = array_values(Permission::whereIn('name', $permissionNames)->pluck('id')->toArray());
        $this->permissions()->attach($permissionIds);
    }

    public function reomvePermissions(array $permissionNames)
    {
        $permissionIds = array_values(Permission::whereIn('name', $permissionNames)->pluck('id')->toArray());
        $this->permissions()->detach($permissionIds);
    }
}
