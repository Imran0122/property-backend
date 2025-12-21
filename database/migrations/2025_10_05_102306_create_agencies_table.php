<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('agencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::table('agents', function (Blueprint $table) {
            $table->foreignId('agency_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('agency_id');
        });
        Schema::dropIfExists('agencies');
    }
};
