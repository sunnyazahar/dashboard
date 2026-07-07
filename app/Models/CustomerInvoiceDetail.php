<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerInvoiceDetail extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'customer_id', 'invoice_recipient_name', 'invoice_email', 'invoice_email_cc', 
        'currency_code', 'payment_terms_days', 'invoice_frequency', 'invoice_remarks', 
        'vat_number', 'eori_number'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
