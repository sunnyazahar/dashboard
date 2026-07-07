<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentOnBoardLeg extends Model
{
    protected $fillable = [
        'shipment_id',
        'departure_date',
        'delivery_date',
        'delivery_time',
        'sort_order',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'delivery_date' => 'date',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }
}
