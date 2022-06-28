<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    public function family()
    {
        return $this->belongsTo(ClientFamily::class, 'family_id', 'id');
    }

    public function scripts()
    {
        return $this->belongsToMany(Script::class, 'clients_scripts', 'client_id', 'script_id');
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
