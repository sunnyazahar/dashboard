<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HubUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'hub_id',
        'name',
        'email',
        'phone_number',
        'show_in_scan_gun',
    ];

    public function hub()
    {
        return $this->belongsTo(Hub::class);
    }
}
