<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerResponsible extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'customer_id',
        'sales_manager_id',
        'account_manager_id',
        'accounting_user_id'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function salesManager()
    {
        return $this->belongsTo(Contact::class, 'sales_manager_id');
    }

    public function accountManager()
    {
        return $this->belongsTo(Contact::class, 'account_manager_id');
    }

    public function accountingUser()
    {
        return $this->belongsTo(Contact::class, 'accounting_user_id');
    }
}
