<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Property;

class BrowseCityController extends Controller
{
    public function index()
    {
        $cities = City::withCount([
            'properties as total_properties' => function ($q) {
                $q->where('status', 'active');
            }
        ])
        ->orderByDesc('total_properties')
        ->limit(10)
        ->get(['id', 'name', 'slug']);

        return response()->json($cities);
    }
}
