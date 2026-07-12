<?php

namespace App\Models;

use App\Traits\LogsFieldChanges;
use App\Traits\TracksUserAudit;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use TracksUserAudit, LogsFieldChanges;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'description',
        'is_main_contact',
        'customer_id',
        'hub_id',
        'supplier_id',
        'other_company_id',
        'office_id',
        'agent_id',
        'reply_to_email',
        'is_cc_enabled',
        'status',
        'category',
        'created_by',
        'updated_by',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function hub()
    {
        return $this->belongsTo(Hub::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function otherCompany()
    {
        return $this->belongsTo(OtherCompany::class, 'other_company_id');
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
