<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Area;
use App\Models\Society;

class AreaGuideController extends Controller
{
    public function index()
    {
        $cities = City::with(['societies' => function($q){
            $q->where('is_popular',1);
        }])->get();

        $data = [];

        foreach($cities as $city){
            $popular = Society::where('city_id',$city->id)
                        ->where('is_popular',1)->get();

            $links = Society::where('city_id',$city->id)->get();

            $data[$city->name] = [
                "popular" => $popular,
                "links" => $links
            ];
        }

        return response()->json($data);
    }

    public function mostViewed()
{
    $cities = \App\Models\City::all();
    $data = [];

    foreach ($cities as $city) {
        $areas = \App\Models\Area::where('city_id', $city->id)
            ->orderBy('views', 'desc')
            ->take(6)
            ->get(['name','slug','views']);

        if($areas->count()){
            $data[$city->name] = $areas;
        }
    }

    return response()->json($data);
}

public function searchCities(Request $request)
{
    $search = $request->search;

    $cities = \App\Models\City::where('name','LIKE',"%$search%")
        ->get(['id','name']);

    return response()->json($cities);
}

}
