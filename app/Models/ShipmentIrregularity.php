<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentIrregularity extends Model
{
    protected $fillable = [
        'shipment_id',
        'irregularity_date',
        'irregularity_type',
        'party_responsible',
        'consequence',
        'extra_cost_mt_usd',
        'status',
        'cause_of_irregularity',
        'action_taken',
        'customer_response',
        'hub_agent_comments',
        'handled_by',
    ];

    protected $casts = [
        'irregularity_date' => 'date',
        'extra_cost_mt_usd' => 'decimal:2',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }
}
