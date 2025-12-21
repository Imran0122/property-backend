<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('package_id')->nullable()->constrained()->onDelete('set null');
            $table->string('gateway'); // stripe | paypal
            $table->string('gateway_id')->nullable(); // stripe session / payment id / paypal order id
            $table->decimal('amount', 15, 2);
            $table->string('currency', 8)->default('PKR');
            $table->string('status')->default('pending'); // pending, succeeded, failed, refunded
            $table->json('payload')->nullable(); // raw response
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
