<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Support\Facades\DB;

class PopularLocationController extends Controller
{
    public function index()
    {
        return response()->json([
            'sale' => [
                'flats'  => $this->getData('active', 'Flat'),
                'houses' => $this->getData('active', 'House'),
                'plots'  => $this->getData('active', 'Plot'),
            ],
            'rent' => [
                'flats'  => $this->getData('rented', 'Flat'),
                'houses' => $this->getData('rented', 'House'),
                'plots'  => $this->getData('rented', 'Plot'),
            ],
        ]);
    }

    private function getData($status, $propertyTypeName)
    {
        return Property::select(
                'cities.name as city',
                'properties.area',
                DB::raw('COUNT(properties.id) as total')
            )
            ->join('cities', 'cities.id', '=', 'properties.city_id')
            ->join('property_types', 'property_types.id', '=', 'properties.property_type_id')
            ->where('properties.status', $status)
            ->where('property_types.name', $propertyTypeName)
            ->groupBy('cities.name', 'properties.area')
            ->orderByDesc('total')
            ->limit(8)
            ->get();
    }
}
