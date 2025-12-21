<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Area;

class AreaController extends Controller
{
    public function getAreasByCity($cityId)
    {
        $areas = Area::where('city_id', $cityId)->get();

        return response()->json([
            'success' => true,
            'city_id' => $cityId,
            'areas' => $areas
        ]);
    }
    public function index()
{
    $areas = Area::all();
    return response()->json($areas);
}

}
