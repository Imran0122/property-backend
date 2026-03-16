<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Amenity;

class AmenityController extends Controller
{
    public function index()
    {

        $amenities = Amenity::select('id','name')
        ->orderBy('name')
        ->get();

        return response()->json([
            'status' => true,
            'data' => $amenities
        ]);
    }
}