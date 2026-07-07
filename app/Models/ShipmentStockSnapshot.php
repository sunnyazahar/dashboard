<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentStockSnapshot extends Model
{
    protected $fillable = [
        'shipment_id',
        'shipment_number',
        'original_crr_id',
        'sort_order',
        'hub_code',
        'vessel_name',
        'po_numbers',
        'supplier',
        'stock_number',
        'pieces_count',
        'total_weight',
        'total_cbm',
        'customs_value',
        'currency',
        'status_label',
        'snapshot_data',
    ];

    protected $casts = [
        'po_numbers' => 'array',
        'pieces_count' => 'integer',
        'total_weight' => 'decimal:2',
        'total_cbm' => 'decimal:4',
        'customs_value' => 'decimal:2',
        'snapshot_data' => 'array',
        'sort_order' => 'integer',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function poNumbersDisplay(): string
    {
        if (is_array($this->po_numbers)) {
            $value = implode(', ', array_filter($this->po_numbers));

            return $value !== '' ? $value : '—';
        }

        return $this->po_numbers ?: '—';
    }
}
