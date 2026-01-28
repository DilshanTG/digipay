<?php

namespace App\Models;

class Payment extends Model
{
    protected string $table = 'payments';

    protected array $fillable = [
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
        'real_description',
        'fake_description',
    ];

    protected array $casts = [
        'amount' => 'decimal:2',
        'meta_data' => 'array',
    ];

    public function merchant(): ?Merchant
    {
        if (!isset($this->attributes['merchant_id'])) {
            return null;
        }

        return Merchant::find($this->attributes['merchant_id']);
    }

    public function getCustomerNameAttribute(): string
    {
        $meta = $this->meta_data;
        if (!$meta) {
            return 'Valued Customer';
        }

        $first = $meta['first_name'] ?? '';
        $last = $meta['last_name'] ?? '';
        $name = trim($first . ' ' . $last);

        return $name ?: 'Valued Customer';
    }
}
