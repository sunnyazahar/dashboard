<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrrChangeLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'crr_id',
        'user_id',
        'title',
        'description',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function crr(): BelongsTo
    {
        return $this->belongsTo(Crr::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
