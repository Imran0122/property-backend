<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('property_features', function (Blueprint $table) {

            $table->string('flooring')->nullable();
            $table->integer('parking_spaces')->nullable();
            $table->boolean('electricity_backup')->default(false);
            $table->boolean('central_ac')->default(false);
            $table->boolean('central_heating')->default(false);
            $table->boolean('double_glazed_windows')->default(false);

            $table->integer('kitchens')->nullable();
            $table->boolean('drawing_room')->default(false);
            $table->boolean('study_room')->default(false);
            $table->boolean('store_room')->default(false);
            $table->boolean('servant_quarter')->default(false);
            $table->boolean('prayer_room')->default(false);
            $table->boolean('dining_room')->default(false);

            $table->string('nearby_schools')->nullable();
            $table->string('nearby_hospitals')->nullable();
            $table->string('nearby_restaurants')->nullable();
            $table->string('nearby_shopping_malls')->nullable();
            $table->integer('distance_from_airport')->nullable();
            $table->string('nearby_public_transport')->nullable();
        });
    }

    public function down(): void
    {
        //
    }
};