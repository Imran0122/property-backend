<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('auto_utilization_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('category'); // hot-ad, super-hot-ad, refreshment-credits
            $table->integer('percentage')->default(0); // 0-100
            $table->string('scope')->default('self'); // self, agency_wide
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_utilization_settings');
    }
};