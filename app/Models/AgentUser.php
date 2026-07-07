<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentUser extends Model
{
    protected $fillable = [
        'agent_id',
        'name',
        'email',
        'phone_number',
        'description',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
