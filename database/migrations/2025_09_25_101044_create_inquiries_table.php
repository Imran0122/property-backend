<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->text('message')->nullable();

            $table->enum('status', ['new','contacted','closed'])->default('new');
            $table->timestamps();

            $table->foreign('property_id')
                  ->references('id')->on('properties')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inquiries');
    }
};
