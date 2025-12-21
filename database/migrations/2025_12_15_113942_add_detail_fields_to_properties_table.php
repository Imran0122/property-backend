<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {

            $table->enum('purpose', ['sale','rent'])
                  ->default('sale')
                  ->after('price');

            $table->decimal('area_size', 10, 2)
                  ->nullable()
                  ->after('area');

            $table->enum('area_unit', ['marla','kanal','sqft'])
                  ->default('marla')
                  ->after('area_size');

            $table->decimal('latitude', 10, 7)
                  ->nullable()
                  ->after('area_unit');

            $table->decimal('longitude', 10, 7)
                  ->nullable()
                  ->after('latitude');
        });
    }

    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'purpose',
                'area_size',
                'area_unit',
                'latitude',
                'longitude'
            ]);
        });
    }
};
