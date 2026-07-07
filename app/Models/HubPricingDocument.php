<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HubPricingDocument extends Model
{
    protected $fillable = [
        'hub_id', 'file_name', 'file_path', 'file_type'
    ];

    public function hub()
    {
        return $this->belongsTo(Hub::class);
    }
}
