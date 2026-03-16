<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::create('construction_rates', function (Blueprint $table) {
        $table->id();
        $table->foreignId('city_id')->constrained()->cascadeOnDelete();
        $table->foreignId('construction_type_id')->constrained()->cascadeOnDelete();
        $table->foreignId('construction_mode_id')->constrained()->cascadeOnDelete();
        $table->decimal('rate_per_sqft', 10, 2);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('construction_rates');
    }
};
