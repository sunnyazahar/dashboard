<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentReleaseLeg extends Model
{
    protected $fillable = [
        'shipment_id',
        'freight_company',
        'delivery_date',
        'delivery_time',
        'sort_order',
    ];

    protected $casts = [
        'delivery_date' => 'date',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }
}
