<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SocietyController extends Controller
{
   public function mostViewedSocieties()
{
    $cities = City::with(['societies' => function ($query) {
        $query->orderBy('views', 'desc')
              ->take(6);
    }])->get();

    return response()->json($cities);
}

}



