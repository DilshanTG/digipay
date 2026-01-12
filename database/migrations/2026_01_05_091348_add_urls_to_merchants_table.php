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
        Schema::table('merchants', function (Blueprint $table) {
            $table->string('return_url')->nullable()->after('allowed_domains');
            $table->string('cancel_url')->nullable()->after('return_url');
            $table->string('notify_url')->nullable()->after('cancel_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropColumn(['return_url', 'cancel_url', 'notify_url']);
        });
    }
};
