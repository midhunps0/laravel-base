<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientScript extends Model
{
    use HasFactory;

    protected $table = "clients_scripts";

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function script()
    {
        return $this->belongsTo(Script::class, 'client_id', 'id');
    }

    public function scopeUserAccessControlled(Builder $query, $checkVal='c.rm_id', $user = null)
    {
        $user = $user ?? auth()->user();
        if ($user->hasRole('Dealer')) {
            $query->where($checkVal, $user->id);
        } else if ($user->hasRole('Team Leader')) {
            $dealers = array_values(User::where('teamleader_id', $user->id)->pluck('id')->toArray());
            $dealers[] = $user->id;
            $query->whereIn($checkVal, $dealers);
        }
        return $query;
    }
}
