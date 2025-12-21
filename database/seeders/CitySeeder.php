<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = ['Karachi', 'Lahore', 'Islamabad', 'Rawalpindi', 'Peshawar'];

        foreach ($cities as $city) {
            DB::table('cities')->insert([
                'name' => $city,
                'slug' => Str::slug($city),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
