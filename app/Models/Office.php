<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_name', 'office_short_name', 'phone_number', 'email', 'eori_number',
        'address', 'city', 'district_state', 'zip_code', 'country_id',
        'postal_address', 'postal_city', 'postal_district_state', 'postal_zip_code', 'office_country_id',
        'invoicing_currency', 'reporting_currency', 'vat_rates', 'vat_country_specific_name', 'vat_number',
        'invoicing_emails', 'heading_invoice', 'information_invoice',
        'use_vat_check', 'show_imo', 'enable_reader', 'status'
    ];

    public function bankAccounts()
    {
        return $this->hasMany(OfficeBankAccount::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
