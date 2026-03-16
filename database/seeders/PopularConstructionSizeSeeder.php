<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\PopularConstructionSize;
class PopularConstructionSizeSeeder extends Seeder
{
    

public function run()
{
    PopularConstructionSize::insert([
        [
            'city_id' => 1,
            'size' => 3,
            'unit_id' => 1
        ],
        [
            'city_id' => 1,
            'size' => 5,
            'unit_id' => 1
        ],
    ]);
}
}
