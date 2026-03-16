<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::create('popular_construction_sizes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('city_id')->constrained()->cascadeOnDelete();
        $table->integer('marla_size');
        $table->integer('covered_area_sqft');
        $table->integer('display_order')->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('popular_construction_sizes');
    }
};
