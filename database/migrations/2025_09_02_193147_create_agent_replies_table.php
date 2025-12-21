<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_replies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_message_id');
            $table->text('reply_message');
            $table->timestamps();

            $table->foreign('agent_message_id')
                  ->references('id')->on('agent_messages')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_replies');
    }
};
