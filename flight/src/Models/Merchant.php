<?php

namespace App\Models;

class Merchant extends Model
{
    protected string $table = 'merchants';

    protected array $fillable = [
        'name',
        'api_key',
        'secret_key',
        'allowed_domains',
        'return_url',
        'cancel_url',
        'notify_url',
        'is_active',
        'sandbox_mode',
    ];

    protected array $casts = [
        'allowed_domains' => 'array',
        'is_active' => 'boolean',
        'sandbox_mode' => 'boolean',
    ];

    public function payments(): array
    {
        if (!isset($this->attributes['id'])) {
            return [];
        }

        return Payment::whereAll('merchant_id', $this->attributes['id']);
    }
}
