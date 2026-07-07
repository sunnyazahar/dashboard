<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'customer_id', 'type', 'street', 'city', 'state', 'zip_code', 'country_id', 'port_code'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
