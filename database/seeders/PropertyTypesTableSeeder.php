<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PropertyType;

class PropertyTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'House'],
            ['name' => 'Plot'],
            ['name' => 'Apartment'],
            ['name' => 'Commercial'],
        ];

        foreach ($types as $type) {
            PropertyType::create($type);
        }
    }
}
