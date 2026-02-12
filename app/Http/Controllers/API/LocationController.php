<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Property;

class LocationController extends Controller
{
    
    public function popularLocations()
{
    $data = \App\Models\Property::where('status', 'active')
        ->with(['city:id,name', 'location:id,name', 'propertyType:id,name'])
        ->selectRaw('purpose, property_type_id, city_id, area_id, COUNT(*) as total')
        ->groupBy('purpose', 'property_type_id', 'city_id', 'area_id')
        ->having('total', '>', 0)
        ->orderByDesc('total')
        ->get();

    return response()->json([
        'status' => true,
        'data' => $data
    ]);
}

}
