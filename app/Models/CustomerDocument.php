<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDocument extends Model
{
    protected $fillable = [
        'customer_id', 'file_name', 'file_path', 'file_type'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function fileUrl(): string
    {
        return route('customers.documents.show', [$this->customer_id, $this->id], false);
    }
}
