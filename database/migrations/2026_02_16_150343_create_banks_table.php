<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::create('banks', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('logo')->nullable();
        $table->decimal('interest_rate', 5, 2)->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('banks');
}


};
