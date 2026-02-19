<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PropertyType;
use App\Models\City;

class ToolsController extends Controller
{
    public function usefulLinks()
    {
        $propertyTypes = PropertyType::all();
        $cities = City::limit(8)->get(); // limit like zameen style

        $data = [];

        foreach ($propertyTypes as $type) {

            $links = [];

            foreach ($cities as $city) {

                $links[] = [
                    'title' => $type->name . ' for Sale in ' . $city->name,
                    'city' => $city->name,
                    'url' => '/' . $type->slug . '-for-sale/' . $city->slug
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
