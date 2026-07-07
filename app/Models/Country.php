<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'name', 'iso_code', 'iso3_code', 'currency', 'currency_value', 'phone_code', 'flag_url', 'flag_emoji', 'is_active'
    ];
}
