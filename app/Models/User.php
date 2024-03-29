<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'teamleader_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'users_roles', 'user_id', 'role_id');
    }

    public function hasRole($role)
    {
        if (is_int($role)) {
            return in_array($role, array_values($this->roles()->pluck('id')->toArray()));
        } elseif (is_string($role)) {
            return in_array($role, array_values($this->roles()->pluck('name')->toArray()));
        } elseif ($role instanceof Role) {
            return in_array($role->id, array_values($this->roles()->pluck('id')->toArray()));
        }
    }

    public function getPermissionsAttribute()
    {
        $permissions = [];
        foreach ($this->roles as $role) {
            array_push($permissions, ...($role->permissions));
        }
        return collect($permissions);
    }

    public function hasPermissionTo($permission)
    {
        if (is_int($permission)) {
            return in_array($permission, array_values($this->permissions->pluck('id')->toArray()));
        } elseif (is_string($permission)) {
            return in_array($permission, array_values($this->permissions->pluck('name')->toArray()));
        } elseif ($permission instanceof Permission) {
            return in_array($permission->id, array_values($this->permissions->pluck('id')->toArray()));
        }
    }

    public function assignRole($role)
    {
        if (is_int($role)) {
            $theRole = Role::find($role);
        } elseif (is_string($role)) {
            $theRole = Role::where('name', $role)->get()->first();
        } elseif ($role instanceof Role) {
            $theRole = $role;
        }
        if ($theRole == null) {
            throw new ModelNotFoundException("Role not found in function assignRole().");
        }
        $this->roles()->attach($theRole->id);
    }

    public function removeRole($role)
    {
        if (is_int($role)) {
            $theRole = Role::find($role);
        } elseif (is_string($role)) {
            $theRole = Role::where('name', $role)->get()->first();
        } elseif ($role instanceof Role) {
            $theRole = $role;
        }
        if ($theRole == null) {
            throw new ModelNotFoundException("Role not found in function assignRole().");
        }
        $this->roles()->detach($theRole->id);
    }

    public function scopeWithRoles($query, $roles)
    {
        if (count($roles) == 0) {
            return $query;
        }
        return $query->whereHas('roles', function($q) use ($roles){
            if (is_int($roles[0])) {
                $q->whereIn('id', $roles);
            } elseif (is_string($roles[0])) {
                $q->whereIn('name', $roles);
            }
        });
    }

    public function teamLeader()
    {
        return $this->belongsTo(User::class, 'teamleader_id', 'id');
    }

    public function dealers()
    {
        return $this->hasMany(User::class, 'teamleader_id', 'id');
    }

    public function clients()
    {
        return $this->hasMany(Client::class, 'rm_id', 'id');
    }

    // public static function boot() {
    //     parent::boot();

    //     static::deleting(function($user) {
    //          $user->roles()->delete();
    //     });
    // }
}
