<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\City;
use App\Models\PropertyType;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::with(['city', 'type', 'images']);

        // ✅ City filter
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // ✅ Property type filter
        if ($request->filled('property_type_id')) {
            $query->where('property_type_id', $request->property_type_id);
        }

        // ✅ Price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // ✅ Sorting
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'high':
                    $query->orderBy('price', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc'); // default newest
        }

        // ✅ Get properties with pagination
        $properties = $query->paginate(9)->withQueryString();

        // ✅ Cities + Property Types for dropdowns
        $cities = City::all();
        $types = PropertyType::all();

        return view('search', compact('properties', 'cities', 'types'));
    }
}
