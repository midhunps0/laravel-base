<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeBackupItem extends Model
{
    use HasFactory;

    protected $table = 'trade_backup_items';

    public $timestamps = false;

    protected $fillable = [
        'date',
        'client_id',
        'script_id',
        'trade_no'
    ];
}
