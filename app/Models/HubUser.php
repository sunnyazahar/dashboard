<?php

namespace App\Models;

use App\Traits\TracksUserAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HubUser extends Model
{
    use HasFactory, TracksUserAudit;

    protected $fillable = [
        'hub_id',
        'name',
        'email',
        'phone_number',
        'show_in_scan_gun',
        'created_by',
        'updated_by',
    ];

    public function hub()
    {
        return $this->belongsTo(Hub::class);
    }
}
