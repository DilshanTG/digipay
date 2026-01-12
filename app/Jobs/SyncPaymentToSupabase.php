<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\SupabaseService;
use App\Models\Payment;

class SyncPaymentToSupabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function handle(SupabaseService $supabase): void
    {
        $supabase->recordPayment([
            'order_id' => $this->payment->order_id,
            'client_order_id' => $this->payment->client_order_id,
            'merchant_id' => $this->payment->merchant_id,
            'amount' => (float) $this->payment->amount,
            'currency' => $this->payment->currency,
            'status' => $this->payment->status,
            'mode' => $this->payment->mode,
            'payhere_ref' => $this->payment->payhere_ref,
            'customer_email' => $this->payment->customer_email,
            'customer_phone' => $this->payment->customer_phone,
            'real_description' => $this->payment->real_description,
            'fake_description' => $this->payment->fake_description,
            'created_at' => $this->payment->created_at->toIso8601String(),
            'updated_at' => $this->payment->updated_at->toIso8601String()
        ]);
    }
}
