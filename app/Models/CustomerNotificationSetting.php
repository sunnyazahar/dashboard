<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerNotificationSetting extends Model
{
    protected $fillable = [
        'customer_id',
        'notify_stock_items',
        'send_automatic_first_mile_email',
        'notify_first_mile_email_sent',
        'shipping_free_storage_days',
        'shipping_free_storage_weight',
        'shipping_free_storage_volume',
        'notify_free_storage_exceeded'
    ];

    protected $casts = [
        'send_automatic_first_mile_email' => 'boolean',
        'shipping_free_storage_weight' => 'decimal:2',
        'shipping_free_storage_volume' => 'decimal:2'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
