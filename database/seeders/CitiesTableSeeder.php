<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            ['name' => 'Lahore'],
            ['name' => 'Karachi'],
            ['name' => 'Islamabad'],
            ['name' => 'Rawalpindi'],
            ['name' => 'Faisalabad'],
            ['name' => 'Multan'],
            ['name' => 'Peshawar'],
            ['name' => 'Quetta'],
            ['name' => 'Gujranwala'],
            ['name' => 'Sialkot'],
            ['name' => 'Hyderabad'],
            ['name' => 'Sargodha'],
            ['name' => 'Bahawalpur'],
            ['name' => 'Abbottabad'],
            ['name' => 'Mardan'],
        ];

        foreach ($cities as $city) {
            City::create($city);
        }
    }
}
