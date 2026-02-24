<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PropertyType;
use App\Models\City;

class ToolsController extends Controller
{
   public function usefulLinks()
{
    $propertyTypes = \App\Models\PropertyType::with(['properties' => function ($query) {
        $query->where('purpose', 'sale')
              ->where('status', 'active');
    }])->get();

    $data = [];

    foreach ($propertyTypes as $type) {

        $cities = $type->properties
            ->pluck('city_id')
            ->unique()
            ->take(5);

        if ($cities->isEmpty()) {
            continue;
        }

        $cityModels = \App\Models\City::whereIn('id', $cities)->get();

        $links = [];

        foreach ($cityModels as $city) {

            $links[] = [
                'title' => $type->name . ' for Sale in ' . $city->name,
                'city' => $city->name,
                'url' => '/' . \Str::slug($type->name) . '-for-sale/' . \Str::slug($city->name)
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
