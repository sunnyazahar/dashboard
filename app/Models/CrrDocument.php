<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrrDocument extends Model
{
    protected $fillable = [
        'crr_id', 'file_name', 'file_path', 'file_type'
    ];

    public function crr()
    {
        return $this->belongsTo(Crr::class);
    }
}
