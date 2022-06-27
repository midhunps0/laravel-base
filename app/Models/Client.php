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
}
