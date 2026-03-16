<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;
use Illuminate\Support\Str;

class PropertiesTableSeeder extends Seeder
{
    public function run()
    {
        Property::insert([

            // 🏠 HOUSE - LAHORE
            [
                'property_type_id' => 1,
                'user_id' => 1,
                'title' => 'Luxury 10 Marla House in DHA Lahore',
                'slug' => Str::slug('Luxury 10 Marla House in DHA Lahore') . '-' . time(),
                'description' => 'Beautifully designed 10 Marla house located in DHA Phase 5, Lahore.',
                'city_id' => 1,
                'area_id' => 1,

                'area' => '10 Marla',
                'area_size' => 10,
                'area_unit' => 'Marla',

                'bedrooms' => 4,
                'bathrooms' => 4,

                'price' => 45000000,
                'purpose' => 'sale',
                'status' => 'available',
                'is_featured' => 1,
                'featured_until' => now()->addDays(30),

                'latitude' => null,
                'longitude' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 🌆 PLOT - KARACHI
            [
                'property_type_id' => 2,
                'user_id' => 1,
                'title' => '1 Kanal Plot in Bahria Town Karachi',
                'slug' => Str::slug('1 Kanal Plot in Bahria Town Karachi') . '-' . time(),
                'description' => 'Prime location residential plot in Bahria Town Karachi.',
                'city_id' => 2,
                'area_id' => 2,

                'area' => '1 Kanal',
                'area_size' => 1,
                'area_unit' => 'Kanal',

                'bedrooms' => null,
                'bathrooms' => null,

                'price' => 15000000,
                'purpose' => 'sale',
                'status' => 'available',
                'is_featured' => 0,
                'featured_until' => null,

                'latitude' => null,
                'longitude' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 🏢 COMMERCIAL - LAHORE
            [
                'property_type_id' => 3,
                'user_id' => 1,
                'title' => 'Commercial Shop in Johar Town',
                'slug' => Str::slug('Commercial Shop in Johar Town') . '-' . time(),
                'description' => 'Ground floor commercial shop available in Johar Town Lahore.',
                'city_id' => 1,
                'area_id' => 1,

                'area' => '500 Sq Ft',
                'area_size' => 500,
                'area_unit' => 'Sq Ft',

                'bedrooms' => null,
                'bathrooms' => 1,

                'price' => 20000000,
                'purpose' => 'sale',
                'status' => 'available',
                'is_featured' => 0,
                'featured_until' => null,

                'latitude' => null,
                'longitude' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}