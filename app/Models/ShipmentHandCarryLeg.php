<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentHandCarryLeg extends Model
{
    protected $fillable = [
        'shipment_id',
        'departure_date',
        'arrival_date',
        'arrival_time',
        'contact_name',
        'contact_phone',
        'onboard_hand_carry',
        'sort_order',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'arrival_date' => 'date',
        'onboard_hand_carry' => 'boolean',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }
}
