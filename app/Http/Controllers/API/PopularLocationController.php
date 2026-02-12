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
                'homes'  => $this->getData('sale', 'Homes'),
                'plots'  => $this->getData('sale', 'Plots'),
                'commercial' => $this->getData('sale', 'Commercial'),
            ],
            'rent' => [
                'homes'  => $this->getData('rent', 'Homes'),
                'plots'  => $this->getData('rent', 'Plots'),
                'commercial' => $this->getData('rent', 'Commercial'),
            ],
        ]);
    }

    private function getData($purpose, $propertyTypeName)
    {
        return Property::select(
                'cities.name as city',
                'areas.name as location',
                DB::raw('COUNT(properties.id) as total')
            )
            ->join('cities', 'cities.id', '=', 'properties.city_id')
            ->join('areas', 'areas.id', '=', 'properties.area_id')
            ->join('property_types', 'property_types.id', '=', 'properties.property_type_id')
            ->where('properties.status', 'active')
            ->where('properties.purpose', $purpose)
            ->where('property_types.name', $propertyTypeName)
            ->groupBy('cities.name', 'areas.name')
            ->orderByDesc('total')
            ->limit(8)
            ->get();
    }
}
