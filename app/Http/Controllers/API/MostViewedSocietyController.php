<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class MostViewedSocietyController extends Controller
{
    public function index()
    {
        $cities = City::with(['societies' => function ($query) {
            $query->orderBy('views', 'desc')
                  ->take(4)
                  ->select('id','city_id','name','slug');
        }])->select('id','name')->get();

        return response()->json([
            'status' => true,
            'data' => $cities
        ]);
    }
}
