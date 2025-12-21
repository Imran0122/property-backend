<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            CitiesTableSeeder::class,
            PropertyTypesTableSeeder::class, // 👈 Ye sabse zaroori hai
            PropertiesTableSeeder::class,
            AdminSeeder::class,
    CitiesTableSeeder::class,
    AreasTableSeeder::class,
    PropertyTypesTableSeeder::class,
    PropertiesTableSeeder::class,
        ]);
    }
}
