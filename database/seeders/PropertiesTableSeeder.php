<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;

class PropertiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample Property 1
        Property::create([
            'title' => 'Luxury 10 Marla House in DHA Lahore',
            'description' => 'Beautifully designed 10 Marla house located in DHA Phase 5, Lahore. Features include 4 bedrooms, spacious lounge, modern kitchen, and imported fittings.',
            'city_id' => 1, // Lahore
            'area' => '10 Marla',
            'bedrooms' => 4,
            'bathrooms' => 4,
            'price' => 45000000, // 4.5 Crore PKR
            'status' => 'available',
            'user_id' => 1,
            'is_featured' => true,
            'property_type_id' => 1, // House
        ]);

        // Sample Property 2
        Property::create([
            'title' => '1 Kanal Plot in Bahria Town Karachi',
            'description' => 'Prime location residential plot in Bahria Town Karachi. Perfect for investment or future construction.',
            'city_id' => 2, // Karachi
            'area' => '1 Kanal',
            'bedrooms' => null,
            'bathrooms' => null,
            'price' => 15000000, // 1.5 Crore PKR
            'status' => 'available',
            'user_id' => 1,
            'is_featured' => false,
            'property_type_id' => 2, // Plot
        ]);

        // Sample Property 3
        Property::create([
            'title' => 'Modern 2 Bed Apartment in Islamabad',
            'description' => 'A fully furnished 2-bedroom apartment in the heart of Islamabad. Ideal for small families or rental income.',
            'city_id' => 3, // Islamabad
            'area' => '950 Sq. Ft',
            'bedrooms' => 2,
            'bathrooms' => 2,
            'price' => 8500000, // 85 Lakh PKR
            'status' => 'available',
            'user_id' => 1,
            'is_featured' => true,
            'property_type_id' => 3, // Apartment
        ]);

        // Sample Property 4
        Property::create([
            'title' => 'Commercial Shop in Saddar, Karachi',
            'description' => '300 Sq. Ft commercial shop located in Saddar, Karachi. High footfall area, perfect for retail businesses.',
            'city_id' => 2, // Karachi
            'area' => '300 Sq. Ft',
            'bedrooms' => null,
            'bathrooms' => 1,
            'price' => 25000000, // 2.5 Crore PKR
            'status' => 'available',
            'user_id' => 1,
            'is_featured' => false,
            'property_type_id' => 4, // Commercial
        ]);
    }
}
