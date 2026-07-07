<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentBillingException extends Model
{
    protected $fillable = [
        'agent_id',
        'office',
        'invoice_to_agent',
        'currency',
        'payment_terms'
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
