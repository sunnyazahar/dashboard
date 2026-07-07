<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Port extends Model
{
    public const TYPE_AIRPORT = 'airport';
    public const TYPE_SEAPORT = 'seaport';

    protected $fillable = [
        'type',
        'iata_code',
        'icao_code',
        'un_locode',
        'port_name',
        'city',
        'country_name',
        'country_code',
        'flag',
        'country_id',
        'latitude',
        'longitude',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_active' => 'boolean',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function displayCode(): ?string
    {
        return $this->type === self::TYPE_AIRPORT
            ? $this->iata_code
            : $this->un_locode;
    }

    public function scopeAirports($query)
    {
        return $query->where('type', self::TYPE_AIRPORT);
    }

    public function scopeSeaports($query)
    {
        return $query->where('type', self::TYPE_SEAPORT);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
