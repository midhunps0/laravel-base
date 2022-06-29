<?php

namespace App\Models;

use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Script extends Model
{
    use HasFactory;

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'clients_scripts',  'script_id', 'client_id');
    }

    public function scopeUserAccessControlled($query, $user = null)
    {
        $user = $user ?? auth()->user();
        if ($user->hasRole('Dealer')) {
            $query->whereIn('rm_id', [$user->id]);
        } else if ($user->hasRole('Team Leader')) {
            $dealers =  array_values(User::where('teamleader_id', $user->id)->pluck('id')->toArray());
            $query->whereIn('rm_id', $dealers);
        }
        return $query;
    }
}
