<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\City;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::withCount('properties')->get();

        return response()->json([
            'status' => 'success',
            'count' => $cities->count(),
            'cities' => $cities->map(function ($city) {
                return [
                    'id' => $city->id,
                    'name' => $city->name,
                    'total_properties' => $city->properties_count
                ];
            })
        ]);
    }
}
