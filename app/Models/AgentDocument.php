<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentDocument extends Model
{
    protected $fillable = [
        'agent_id',
        'section',
        'filename',
        'file_path',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
