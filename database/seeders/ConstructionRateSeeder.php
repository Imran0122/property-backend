<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
 use App\Models\ConstructionRate;

class ConstructionRateSeeder extends Seeder
{
   
public function run()
{
    ConstructionRate::insert([
        [
            'city_id' => 1,
            'construction_type_id' => 1,
            'construction_mode_id' => 1,
            'rate_per_sqft' => 2500
        ],
        [
            'city_id' => 1,
            'construction_type_id' => 2,
            'construction_mode_id' => 1,
            'rate_per_sqft' => 4000
        ],
    ]);
}
}
