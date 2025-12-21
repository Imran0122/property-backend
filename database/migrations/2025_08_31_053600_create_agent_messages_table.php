<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('agent_messages', function (Blueprint $table) {
     $table->id();
        $table->unsignedBigInteger('property_id');
        $table->string('name');
        $table->string('email');
        $table->string('phone');
        $table->text('message');
        $table->enum('status', ['unread', 'read'])->default('unread'); // ✅ new column
        $table->timestamps();

        $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
    });
}

    public function down(): void
    {
        Schema::dropIfExists('agent_messages');
    }


};
