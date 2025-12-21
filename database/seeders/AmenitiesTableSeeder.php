<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AmenitiesTableSeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            ['name' => 'Electricity'],
            ['name' => 'Gas'],
            ['name' => 'Water Supply'],
            ['name' => 'Sewerage'],
            ['name' => 'Internet'],
            ['name' => 'Parking'],
            ['name' => 'Security'],
        ];

        DB::table('amenities')->insert($amenities);
    }
}
