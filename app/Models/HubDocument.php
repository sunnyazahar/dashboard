<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HubDocument extends Model
{
    protected $fillable = [
        'hub_id', 'document_type', 'file_name', 'file_path', 'file_type'
    ];

    public function hub()
    {
        return $this->belongsTo(Hub::class);
    }
}
