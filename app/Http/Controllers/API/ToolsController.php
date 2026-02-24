<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Str;
use App\Models\PropertyType;
use App\Models\City;

class ToolsController extends Controller
{
 public function usefulLinks()
{
    $propertyTypes = PropertyType::all();
    $data = [];

    foreach ($propertyTypes as $type) {

        $cities = City::whereHas('properties', function ($query) use ($type) {
            $query->where('property_type_id', $type->id)
                  ->where('purpose', 'sale')
                  ->where('status', 'active');
        })->limit(5)->get();

        if ($cities->isEmpty()) {
            continue;
        }

        $links = [];

        foreach ($cities as $city) {
            $links[] = [
                'title' => $type->name . ' for Sale in ' . $city->name,
                'city'  => $city->name,
                'url'   => '/' . Str::slug($type->name) . '-for-sale/' . Str::slug($city->name)
            ];
        }

        $data[] = [
            'property_type' => $type->name,
            'links' => $links
        ];
    }

    return response()->json([
        'success' => true,
        'data' => $data
    ]);
}

}
