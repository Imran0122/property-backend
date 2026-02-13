<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\City;
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
}
