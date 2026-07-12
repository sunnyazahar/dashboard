<?php

namespace App\Models;

use App\Traits\LogsFieldChanges;
use App\Traits\TracksUserAudit;
use Illuminate\Database\Eloquent\Model;

class AgentUser extends Model
{
    use TracksUserAudit, LogsFieldChanges;

    protected $fillable = [
        'agent_id',
        'name',
        'email',
        'phone_number',
        'description',
        'created_by',
        'updated_by',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
