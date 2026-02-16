<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Society;

class SocietyMapController extends Controller
{
    // Main Society Maps Page API
    public function index()
    {
        $cities = City::select('id','name')->get();

        $featured = Society::with('city:id,name')
            ->where('is_popular', 1)
            ->orderBy('views', 'desc')
            ->take(4)
            ->get(['id','city_id','name','slug','image','views']);

        if($featured->count() < 4){
            $featured = Society::with('city:id,name')
                ->orderBy('views','desc')
                ->take(4)
                ->get(['id','city_id','name','slug','image','views']);
        }

        $citiesWithCounts = City::withCount('societies')
            ->having('societies_count','>',0)
            ->get(['id','name']);

        return response()->json([
            'status' => true,
            'data' => [
                'cities' => $cities,
                'featured_societies' => $featured,
                'cities_with_counts' => $citiesWithCounts
            ]
        ]);
    }

    // Societies by City (Dropdown)
    public function societiesByCity($id)
    {
        $societies = Society::where('city_id',$id)
            ->select('id','name','slug')
            ->get();

        return response()->json([
            'status'=>true,
            'data'=>$societies
        ]);
    }

    // Society Detail Page
    public function show($slug)
    {
        $society = Society::with('images')
            ->where('slug',$slug)
            ->firstOrFail();

        $society->increment('views');

        return response()->json([
            'status'=>true,
            'data'=>$society
        ]);
    }
}
