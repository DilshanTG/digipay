<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SupabaseService
{
    protected $url;
    protected $key;

    public function __construct()
    {
        // We will add SUPABASE credentials to .env later
        $this->url = env('SUPABASE_URL');
        $this->key = env('SUPABASE_KEY');
    }

    public function recordPayment($paymentData)
    {
        if (!$this->url || !$this->key) {
            Log::warning('Supabase credentials not configured. Skipping sync.');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'apikey' => $this->key,
                'Authorization' => 'Bearer ' . $this->key,
                'Content-Type' => 'application/json',
                'Prefer' => 'return=minimal'
            ])->post($this->url . '/rest/v1/payments', $paymentData);

            if ($response->successful()) {
                return true;
            } else {
                Log::error('Supabase Sync Failed: ' . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Supabase Exception: ' . $e->getMessage());
            return false;
        }
    }
}
