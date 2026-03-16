<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ConstructionRate;
use App\Models\AreaUnit;

class ConstructionCalculatorController extends Controller
{
   public function calculate(Request $request)
{
    $request->validate([
        'city_id' => 'required|exists:cities,id',
        'area_size' => 'required|numeric|min:1',
        'unit_id' => 'required|exists:area_units,id',
        'construction_type_id' => 'required|exists:construction_types,id',
        'construction_mode_id' => 'required|exists:construction_modes,id',
    ]);

    $unit = AreaUnit::find($request->unit_id);

    if (!$unit) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid unit selected'
        ], 404);
    }

    $sqft = $request->area_size * $unit->conversion_to_sqft;

    $rate = ConstructionRate::where('city_id', $request->city_id)
        ->where('construction_type_id', $request->construction_type_id)
        ->where('construction_mode_id', $request->construction_mode_id)
        ->first();

    if (!$rate) {
        return response()->json([
            'status' => false,
            'message' => 'Rate not found for selected combination'
        ], 404);
    }

    $total_cost = $sqft * $rate->rate_per_sqft;

    return response()->json([
        'status' => true,
        'data' => [
            'input_area' => $request->area_size,
            'unit' => $unit->name,
            'converted_sqft' => round($sqft, 2),
            'rate_per_sqft' => $rate->rate_per_sqft,
            'total_cost' => round($total_cost, 2),
        ]
    ]);
}
}
