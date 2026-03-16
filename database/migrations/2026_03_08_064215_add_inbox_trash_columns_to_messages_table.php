<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->boolean('is_trashed_by_receiver')->default(false)->after('is_read');
            $table->timestamp('trashed_at')->nullable()->after('is_trashed_by_receiver');

            $table->index(['receiver_id', 'is_trashed_by_receiver']);
            $table->index(['receiver_id', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['receiver_id', 'is_trashed_by_receiver']);
            $table->dropIndex(['receiver_id', 'is_read']);

            $table->dropColumn(['is_trashed_by_receiver', 'trashed_at']);
        });
    }
};