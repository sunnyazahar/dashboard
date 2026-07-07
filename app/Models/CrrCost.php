<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrrCost extends Model
{
    protected $fillable = [
        'crr_id',
        'type',
        'carrier',
        'net_value',
        'currency',
        'net_value_usd',
        'invoice_no',
        'remarks',
        'hub_agent',
        'tag',
    ];

    protected $casts = [
        'net_value' => 'decimal:2',
        'net_value_usd' => 'decimal:2',
    ];

    public function crr()
    {
        return $this->belongsTo(Crr::class);
    }
}
