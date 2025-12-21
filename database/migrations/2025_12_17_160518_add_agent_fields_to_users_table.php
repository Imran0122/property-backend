<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('users', function (Blueprint $table) {

        if (!Schema::hasColumn('users', 'is_agent')) {
            $table->boolean('is_agent')->default(false);
        }

        if (!Schema::hasColumn('users', 'phone')) {
            $table->string('phone')->nullable();
        }

        if (!Schema::hasColumn('users', 'whatsapp')) {
            $table->string('whatsapp')->nullable();
        }

        if (!Schema::hasColumn('users', 'agency_name')) {
            $table->string('agency_name')->nullable();
        }

        if (!Schema::hasColumn('users', 'agent_photo')) {
            $table->string('agent_photo')->nullable();
        }

        if (!Schema::hasColumn('users', 'bio')) {
            $table->text('bio')->nullable();
        }
    });
}

};
