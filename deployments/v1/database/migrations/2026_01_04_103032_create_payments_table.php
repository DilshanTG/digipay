<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->onDelete('cascade');
            $table->string('order_id')->unique()->index(); // Internal Order ID
            $table->string('client_order_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('LKR');
            $table->string('status')->default('PENDING')->index(); // PENDING, SUCCESS, FAILED
            $table->text('redirect_url')->nullable();
            $table->string('mode')->default('api'); // 'api' or 'direct'
            $table->string('payment_method')->nullable();
            $table->string('payhere_ref')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->json('meta_data')->nullable(); // For flexibility
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
