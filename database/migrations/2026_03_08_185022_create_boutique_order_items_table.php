<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boutique_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boutique_order_id')->constrained('boutique_orders')->cascadeOnDelete();
            $table->foreignId('boutique_product_id')->constrained('boutique_products')->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2)->default(0);
            $table->string('currency', 10)->default('MAD');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boutique_order_items');
    }
};