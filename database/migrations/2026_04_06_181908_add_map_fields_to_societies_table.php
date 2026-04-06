<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('societies', function (Blueprint $table) {
            if (!Schema::hasColumn('societies', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable();
            }

            if (!Schema::hasColumn('societies', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable();
            }

            if (!Schema::hasColumn('societies', 'map_zoom')) {
                $table->unsignedTinyInteger('map_zoom')->default(14);
            }
        });
    }

    public function down(): void
    {
        Schema::table('societies', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('societies', 'latitude')) {
                $columns[] = 'latitude';
            }

            if (Schema::hasColumn('societies', 'longitude')) {
                $columns[] = 'longitude';
            }

            if (Schema::hasColumn('societies', 'map_zoom')) {
                $columns[] = 'map_zoom';
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};