<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeBankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_id', 'bank', 'currency', 'account_number', 'iban', 'swift', 'is_main_account'
    ];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}
