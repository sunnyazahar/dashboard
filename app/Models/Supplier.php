<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'supplier_name', 'phone_number', 'email', 'remarks', 'special_considerations',
        'supplier_address', 'city', 'district_state', 'zip_code', 'country_id', 'port_code',
        'office_address', 'office_city', 'office_district_state', 'office_zip_code', 'office_country_id',
        'vat_number', 'eori_number', 'currency', 'un_locode'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function officeCountry()
    {
        return $this->belongsTo(Country::class, 'office_country_id');
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
