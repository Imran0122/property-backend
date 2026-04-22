<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contact_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('inquiry_type')->default('contact'); // contact, developer, agent
            $table->string('name');
            $table->string('email');
            $table->string('phone', 30);
            $table->string('subject')->nullable();
            $table->text('message');
            $table->string('source_page')->nullable();
            $table->string('status')->default('new');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_inquiries');
    }
};