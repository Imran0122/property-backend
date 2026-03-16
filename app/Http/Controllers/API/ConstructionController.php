<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\ConstructionType;
use App\Models\ConstructionMode;
use App\Models\ConstructionRate;

class ConstructionController extends Controller
{
    public function cities()
    {
        return response()->json([
            'status' => true,
            'data' => City::select('id','name')->get()
        ]);
    }

    public function types()
    {
        return response()->json([
            'status' => true,
            'data' => ConstructionType::select('id','name','slug')->get()
        ]);
    }

    public function modes()
    {
        return response()->json([
            'status' => true,
            'data' => ConstructionMode::select('id','name','slug')->get()
        ]);
    }

    // public function calculate(Request $request)
    // {
    //     $request->validate([
    //         'city_id' => 'required|exists:cities,id',
    //         'construction_type_id' => 'required|exists:construction_types,id',
    //         'construction_mode_id' => 'required|exists:construction_modes,id',
    //         'covered_area' => 'required|numeric|min:1'
    //     ]);

    //     $rate = ConstructionRate::where('city_id', $request->city_id)
    //         ->where('construction_type_id', $request->construction_type_id)
    //         ->where('construction_mode_id', $request->construction_mode_id)
    //         ->first();

    //     if(!$rate){
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Rate not found for selected options'
    //         ],404);
    //     }

    //     $total_cost = $request->covered_area * $rate->rate_per_sqft;

    //     return response()->json([
    //         'status' => true,
    //         'data' => [
    //             'covered_area' => $request->covered_area,
    //             'rate_per_sqft' => $rate->rate_per_sqft,
    //             'total_cost' => $total_cost
    //         ]
    //     ]);
    // }
}