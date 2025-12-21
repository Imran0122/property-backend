<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('location')->nullable();
            $table->string('developer')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('ongoing'); // ongoing, completed
            $table->string('cover_image')->nullable();
            $table->timestamps();

            $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::dropIfExists('projects');
    }
};
