<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentSeaLeg extends Model
{
    protected $fillable = [
        'shipment_id',
        'bill_of_lading',
        'container_number',
        'transport_vessel_imo',
        'transport_vessel_name',
        'etd',
        'eta',
        'arrival_time',
        'sort_order',
    ];

    protected $casts = [
        'etd' => 'date',
        'eta' => 'date',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }
}
