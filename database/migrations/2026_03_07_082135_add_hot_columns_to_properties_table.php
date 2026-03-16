<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {

            $table->boolean('is_hot')->default(0)->after('is_featured');
            $table->boolean('is_super_hot')->default(0)->after('is_hot');

        });
    }

    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {

            $table->dropColumn('is_hot');
            $table->dropColumn('is_super_hot');

        });
    }
};