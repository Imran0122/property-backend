<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PropertyType;
use App\Models\City;

class ToolsController extends Controller
{
    public function usefulLinks()
{
    $propertyTypes = \App\Models\PropertyType::select('name')
                        ->distinct()
                        ->get();

    $cities = \App\Models\City::limit(5)->get();

    $data = [];

    foreach ($propertyTypes as $type) {

        $typeSlug = \Illuminate\Support\Str::slug($type->name);

        $links = [];

        foreach ($cities as $city) {

            $citySlug = \Illuminate\Support\Str::slug($city->name);

            $links[] = [
                'title' => $type->name . ' for Sale in ' . $city->name,
                'city' => $city->name,
                'url' => '/' . $typeSlug . '-for-sale/' . $citySlug
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
