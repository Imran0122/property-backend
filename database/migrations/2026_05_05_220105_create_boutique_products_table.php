<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('boutique_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 10)->default('MAD');
            $table->string('type');
            $table->string('category')->default('announcements');
            $table->string('badge')->nullable();
            $table->boolean('is_recommended')->default(false);
            $table->boolean('requires_property')->default(false);
            $table->boolean('requires_published_property')->default(false);
            $table->string('status')->default('active');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boutique_products');
    }
};