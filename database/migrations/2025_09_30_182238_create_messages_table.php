<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id');     // who sent
            $table->unsignedBigInteger('receiver_id');   // who receives (owner or user)
            $table->unsignedBigInteger('property_id')->nullable(); // optional link to property
            $table->text('body');
            $table->boolean('is_read')->default(false);
            $table->string('type')->default('message'); // 'message' | 'inquiry' | 'offer' etc.
            $table->timestamps();

            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->index(['sender_id','receiver_id','property_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
