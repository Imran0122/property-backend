<?php

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration {
//     public function up(): void {
//         Schema::table('properties', function (Blueprint $table) {
//             $table->boolean('is_featured')->default(false);
//             $table->timestamp('featured_until')->nullable();
//         });
//     }

//     public function down(): void {
//         Schema::table('properties', function (Blueprint $table) {
//             $table->dropColumn(['is_featured', 'featured_until']);
//         });
//     }
// };











use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add columns only if they do not already exist
        if (! Schema::hasColumn('properties', 'is_featured') ||
            ! Schema::hasColumn('properties', 'featured_until')) {

            Schema::table('properties', function (Blueprint $table) {
                if (! Schema::hasColumn('properties', 'is_featured')) {
                    $table->boolean('is_featured')->default(false)->after('status');
                }

                if (! Schema::hasColumn('properties', 'featured_until')) {
                    $table->timestamp('featured_until')->nullable()->after('is_featured');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            if (Schema::hasColumn('properties', 'featured_until')) {
                $table->dropColumn('featured_until');
            }
            if (Schema::hasColumn('properties', 'is_featured')) {
                $table->dropColumn('is_featured');
            }
        });
    }
};
