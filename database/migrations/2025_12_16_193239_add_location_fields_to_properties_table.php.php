<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {

            if (!Schema::hasColumn('properties', 'purpose')) {
                $table->enum('purpose', ['sale','rent'])->after('price');
            }

            if (!Schema::hasColumn('properties', 'area_size')) {
                $table->decimal('area_size', 10, 2)->nullable()->after('area');
            }

            if (!Schema::hasColumn('properties', 'area_unit')) {
                $table->enum('area_unit', ['marla','kanal','sqft'])
                      ->default('marla')
                      ->after('area_size');
            }

            if (!Schema::hasColumn('properties', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable();
            }

            if (!Schema::hasColumn('properties', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable();
            }

        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {

            if (Schema::hasColumn('properties', 'purpose')) {
                $table->dropColumn('purpose');
            }

            if (Schema::hasColumn('properties', 'area_size')) {
                $table->dropColumn('area_size');
            }

            if (Schema::hasColumn('properties', 'area_unit')) {
                $table->dropColumn('area_unit');
            }

            if (Schema::hasColumn('properties', 'latitude')) {
                $table->dropColumn('latitude');
            }

            if (Schema::hasColumn('properties', 'longitude')) {
                $table->dropColumn('longitude');
            }

        });
    }
};
