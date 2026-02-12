<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyType;
use Illuminate\Support\Facades\DB;

class PopularLocationController extends Controller
{
    public function index()
    {
        $purposes = ['sale', 'rent'];
        $propertyTypes = PropertyType::pluck('name');

        $data = [];

        foreach ($purposes as $purpose) {

            foreach ($propertyTypes as $type) {

                $results = Property::select(
                        'cities.name as city',
                        'areas.name as location',
                        DB::raw('COUNT(properties.id) as total')
                    )
                    ->join('cities', 'cities.id', '=', 'properties.city_id')
                    ->join('areas', 'areas.id', '=', 'properties.area_id')
                    ->join('property_types', 'property_types.id', '=', 'properties.property_type_id')
                    ->where('properties.status', 'active')
                    ->where('properties.purpose', $purpose)
                    ->where('property_types.name', $type)
                    ->groupBy('cities.name', 'areas.name')
                    ->orderByDesc('total')
                    ->limit(8)
                    ->get()
                    ->groupBy('city');

                if ($results->isNotEmpty()) {
                    $data[$purpose][$type] = $results;
                }
            }
        }

        return response()->json($data);
    }
}
