<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentManifest extends Model
{
    protected $fillable = [
        'shipment_id',
        'version',
        'file_name',
        'file_path',
        'form_hash',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function fileUrl(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function displayLabel(): string
    {
        return 'manifest' . $this->version;
    }
}
