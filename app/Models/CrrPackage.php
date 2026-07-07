<?php

namespace App\Models;

use App\Casts\JsonArrayCast;
use Illuminate\Database\Eloquent\Model;

class CrrPackage extends Model
{
    protected $fillable = [
        'crr_id',
        'length',
        'width',
        'height',
        'weight',
        'cbm',
        'warehouse_location',
        'remarks',
        'is_dgr',
        'dgr_description',
        'un_number',
        'dgr_class',
        'is_delivery_irregularity',
        'delivery_irregularities',
        'is_not_stackable',
        'is_medicine',
        'is_xray',
    ];

    protected $casts = [
        'is_dgr' => 'boolean',
        'is_delivery_irregularity' => 'boolean',
        'delivery_irregularities' => JsonArrayCast::class,
        'is_not_stackable' => 'boolean',
        'is_medicine' => 'boolean',
        'is_xray' => 'boolean',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
        'cbm' => 'decimal:4',
    ];

    public function crr()
    {
        return $this->belongsTo(Crr::class);
    }
}
