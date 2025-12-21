<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Area;
use App\Models\City;

class AreasTableSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            'Lahore' => ['DHA', 'Bahria Town', 'Model Town', 'Gulberg', 'Johar Town'],
            'Karachi' => ['Clifton', 'Gulshan-e-Iqbal', 'DHA Karachi', 'North Nazimabad', 'Korangi'],
            'Islamabad' => ['F-6', 'F-7', 'G-10', 'E-11', 'Blue Area'],
            'Rawalpindi' => ['Saddar', 'Chaklala Scheme', 'Bahria Town Rawalpindi', 'Defence', 'Satellite Town'],
            'Faisalabad' => ['Peoples Colony', 'Gulberg', 'Samanabad', 'Millat Town', 'Eden Valley'],
            'Multan' => ['Gulgasht Colony', 'Shah Rukn-e-Alam', 'Wapda Town', 'Cantt', 'Model Town'],
            'Peshawar' => ['University Town', 'Hayatabad', 'DHA Peshawar', 'Gulbahar', 'Saddar'],
        ];

        foreach ($areas as $cityName => $areaNames) {
            $city = City::where('name', $cityName)->first();
            if ($city) {
                foreach ($areaNames as $area) {
                    Area::create([
                        'city_id' => $city->id,
                        'name' => $area
                    ]);
                }
            }
        }
    }
}
