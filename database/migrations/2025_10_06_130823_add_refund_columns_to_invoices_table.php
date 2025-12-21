<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('invoices', function (Blueprint $table) {
            $table->timestamp('refunded_at')->nullable();
            $table->string('refund_id')->nullable(); // Stripe/PayPal refund ref
        });
    }

    public function down(): void {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['refunded_at','refund_id']);
        });
    }
};
