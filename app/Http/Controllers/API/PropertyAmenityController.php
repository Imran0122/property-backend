<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PropertyAmenity;

class PropertyAmenityController extends Controller
{

    // save amenities
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'amenities' => 'required|array'
        ]);

        foreach ($request->amenities as $amenity_id) {
            PropertyAmenity::create([
                'property_id' => $request->property_id,
                'amenity_id' => $amenity_id
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Amenities saved successfully'
        ]);
    }


    // get amenities of property
    public function show($property_id)
    {
        $amenities = PropertyAmenity::where('property_id', $property_id)->get();

        return response()->json([
            'status' => true,
            'amenities' => $amenities
        ]);
    }

}