<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AreaUnitSeeder extends Seeder
{
    public function run()
{
    AreaUnit::insert([
        ['name' => 'Marla'],
        ['name' => 'Kanal'],
        ['name' => 'Sq Ft'],
    ]);
}
}
