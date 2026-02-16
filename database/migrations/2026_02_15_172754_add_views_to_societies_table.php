<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
public function up()
{
    Schema::table('societies', function (Blueprint $table) {
        if (!Schema::hasColumn('societies', 'views')) {
            $table->unsignedBigInteger('views')->default(0)->after('description');
        }
    });
}

public function down()
{
    Schema::table('societies', function (Blueprint $table) {
        $table->dropColumn('views');
    });
}


};
