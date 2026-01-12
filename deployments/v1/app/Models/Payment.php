<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'merchant_id',
        'order_id',
        'client_order_id',
        'amount',
        'currency',
        'status',
        'redirect_url',
        'notify_url',
        'mode',
        'payment_method',
        'payhere_ref',
        'customer_email',
        'customer_phone',
        'meta_data',
        'real_description', // Store actual description here
        'fake_description', // Store what PayHere sees
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'meta_data' => 'array',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function getCustomerNameAttribute()
    {
        $meta = $this->meta_data;
        if (!$meta) return 'Valued Customer';
        
        $first = $meta['first_name'] ?? '';
        $last = $meta['last_name'] ?? '';
        $name = trim($first . ' ' . $last);
        
        return $name ?: 'Valued Customer';
    }
}
