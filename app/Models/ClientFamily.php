<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientFamily extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function clients()
    {
        return $this->hasMany(Client::class, 'family_id', 'id');
    }
}
