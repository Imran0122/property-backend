<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertyImagesTableSeeder extends Seeder
{
    public function run(): void
    {
        $properties = DB::table('properties')->pluck('id')->toArray();

        foreach ($properties as $propertyId) {
            for ($i = 1; $i <= 3; $i++) {
                DB::table('property_images')->insert([
                    'property_id' => $propertyId,
                    'image_path'  => "properties/property{$propertyId}_img{$i}.jpg",
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }
    }
}
