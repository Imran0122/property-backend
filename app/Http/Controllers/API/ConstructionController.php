<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AreaUnit;
use App\Models\City;
use App\Models\ConstructionMode;
use App\Models\ConstructionType;
use App\Models\PopularConstructionSize;

class ConstructionController extends Controller
{
    public function meta()
    {
        return response()->json([
            'status' => true,
            'data' => [
                'cities' => City::select('id', 'name')->get(),
                'units' => AreaUnit::select('id', 'name', 'slug', 'conversion_to_sqft')->get(),
                'types' => ConstructionType::select('id', 'name', 'slug')->get(),
                'modes' => ConstructionMode::select('id', 'name', 'slug')->get(),
                'popular' => $this->popularData(),
            ]
        ]);
    }

    public function cities()
    {
        return response()->json([
            'status' => true,
            'data' => City::select('id', 'name')->get()
        ]);
    }

    public function units()
    {
        return response()->json([
            'status' => true,
            'data' => AreaUnit::select('id', 'name', 'slug', 'conversion_to_sqft')->get()
        ]);
    }

    public function types()
    {
        return response()->json([
            'status' => true,
            'data' => ConstructionType::select('id', 'name', 'slug')->get()
        ]);
    }

    public function modes()
    {
        return response()->json([
            'status' => true,
            'data' => ConstructionMode::select('id', 'name', 'slug')->get()
        ]);
    }

    public function popular()
    {
        return response()->json([
            'status' => true,
            'data' => $this->popularData()
        ]);
    }

    private function popularData()
    {
        $rows = PopularConstructionSize::query()->get();

        if ($rows->count()) {
            return $rows->map(function ($row) {
                return [
                    'title' => $row->title ?? '',
                    'area_size' => $row->area_size ?? null,
                    'unit' => $row->unit ?? 'Marla',
                    'sqft' => $row->sqft ?? null,
                    'city' => $row->city ?? 'Casablanca',
                    'storeys' => $row->storeys ?? 'À deux étages',
                ];
            })->values();
        }

        return collect([
            ['title' => '3 Coût de construction par Are', 'area_size' => 3, 'unit' => 'Marla', 'sqft' => 1215, 'city' => 'Casablanca', 'storeys' => 'À deux étages'],
            ['title' => '4 Coût de construction par Are', 'area_size' => 4, 'unit' => 'Marla', 'sqft' => 1620, 'city' => 'Casablanca', 'storeys' => 'À deux étages'],
            ['title' => '5 Coût de construction par Are', 'area_size' => 5, 'unit' => 'Marla', 'sqft' => 2025, 'city' => 'Casablanca', 'storeys' => 'À deux étages'],
            ['title' => '6 Coût de construction par Are', 'area_size' => 6, 'unit' => 'Marla', 'sqft' => 2295, 'city' => 'Casablanca', 'storeys' => 'À deux étages'],
            ['title' => '7 Coût de construction par Are', 'area_size' => 7, 'unit' => 'Marla', 'sqft' => 2678, 'city' => 'Casablanca', 'storeys' => 'À deux étages'],
            ['title' => '8 Coût de construction par Are', 'area_size' => 8, 'unit' => 'Marla', 'sqft' => 3060, 'city' => 'Casablanca', 'storeys' => 'À deux étages'],
            ['title' => '10 Coût de construction par Are', 'area_size' => 10, 'unit' => 'Marla', 'sqft' => 3375, 'city' => 'Casablanca', 'storeys' => 'À deux étages'],
        ])->values();
    }
}