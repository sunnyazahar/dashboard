<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentFlight extends Model
{
    protected $fillable = [
        'shipment_id',
        'leg_reference',
        'flight_number',
        'departure_date',
        'arrival_date',
        'arrival_time',
        'sort_order',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'arrival_date' => 'date',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }
}
