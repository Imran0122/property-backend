<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
{
    Schema::table('agencies', function (Blueprint $table) {
        if (!Schema::hasColumn('agencies', 'user_id')) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
        }
        if (!Schema::hasColumn('agencies', 'slug')) {
            $table->string('slug')->nullable()->after('name');
        }
        if (!Schema::hasColumn('agencies', 'status')) {
            $table->string('status')->default('pending')->after('slug');
        }
        if (!Schema::hasColumn('agencies', 'city')) {
            $table->string('city')->nullable();
        }
        if (!Schema::hasColumn('agencies', 'website')) {
            $table->string('website')->nullable();
        }
    });
}

public function down()
{
    Schema::table('agencies', function (Blueprint $table) {
        $table->dropColumn(['user_id', 'slug', 'status', 'city', 'website']);
    });
}
};
