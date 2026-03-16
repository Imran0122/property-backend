<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
 use App\Models\ConstructionMode;


class ConstructionModeSeeder extends Seeder
{
   
public function run()
{
    ConstructionMode::insert([
        ['name' => 'With Material'],
        ['name' => 'Without Material'],
    ]);
}
}
