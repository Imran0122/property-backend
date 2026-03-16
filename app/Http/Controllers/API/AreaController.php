<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function getAreasByCity($cityId)
    {
        $areas = Area::withCount('properties')
            ->where('city_id', $cityId)
            ->orderBy('name')
            ->get(['id', 'city_id', 'name', 'slug', 'views']);

        return response()->json([
            'success' => true,
            'city_id' => (int) $cityId,
            'areas' => $areas
        ]);
    }

    public function index()
    {
        $areas = Area::with('city:id,name')
            ->withCount('properties')
            ->orderBy('name')
            ->get(['id', 'city_id', 'name', 'slug', 'views']);

        return response()->json([
            'success' => true,
            'data' => $areas
        ]);
    }
}