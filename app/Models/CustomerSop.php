<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerSop extends Model
{
    protected $fillable = [
        'customer_id',
        'send_stocklist',
        'onboard_delivery',
        'quotes_prior_to_instructions',
        'agreed_rate',
        'invoicing_procedure',
        'pending_entry',
        'special_pending_routines',
        'other_procedures_comments'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
