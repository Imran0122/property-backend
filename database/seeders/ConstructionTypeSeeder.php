<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
 use App\Models\ConstructionType;

class ConstructionTypeSeeder extends Seeder
{

public function run()
{
    ConstructionType::insert([
        ['name' => 'Grey Structure'],
        ['name' => 'Complete'],
    ]);
}
}
