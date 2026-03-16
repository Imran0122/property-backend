<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'mobile')) {
                $table->string('mobile')->nullable();
            }

            if (!Schema::hasColumn('users', 'landline')) {
                $table->string('landline')->nullable();
            }

            if (!Schema::hasColumn('users', 'whatsapp')) {
                $table->string('whatsapp')->nullable();
            }

            if (!Schema::hasColumn('users', 'city_id')) {
                $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            }

            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable();
            }

            if (!Schema::hasColumn('users', 'profile_image')) {
                $table->string('profile_image')->nullable();
            }

            if (!Schema::hasColumn('users', 'currency')) {
                $table->string('currency', 10)->default('MAD');
            }

            if (!Schema::hasColumn('users', 'area_unit')) {
                $table->string('area_unit', 20)->default('m²');
            }

            if (!Schema::hasColumn('users', 'email_notifications')) {
                $table->boolean('email_notifications')->default(false);
            }

            if (!Schema::hasColumn('users', 'newsletters')) {
                $table->boolean('newsletters')->default(false);
            }

            if (!Schema::hasColumn('users', 'automated_reports')) {
                $table->boolean('automated_reports')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        // pehle foreign key drop karne ki koshish
        if (Schema::hasColumn('users', 'city_id')) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropForeign(['city_id']);
                });
            } catch (\Throwable $e) {
                // agar foreign key exist na karti ho to ignore
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'mobile',
                'landline',
                'whatsapp',
                'city_id',
                'address',
                'profile_image',
                'currency',
                'area_unit',
                'email_notifications',
                'newsletters',
                'automated_reports',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};