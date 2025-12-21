<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Area;

class AreaController extends Controller
{
    // GET all areas (optional)
    public function index()
    {
        return response()->json([
            'success' => true,
            'areas' => Area::all()
        ]);
    }

    // GET areas by city ID
    public function getAreasByCity($cityId)
    {
        $areas = Area::where('city_id', $cityId)->get();

        if ($areas->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No areas found for this city'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'city_id' => $cityId,
            'areas' => $areas
        ]);
    }
}
