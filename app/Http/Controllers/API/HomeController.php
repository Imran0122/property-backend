<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;

class HomeController extends Controller
{
   public function projects()
{
    $projects = \App\Models\Project::where('is_featured', 1)
        ->with('city:id,name,slug')
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get([
            'id',
            'title',
            'slug',
            'city_id',
            'location',
            'developer',
            'cover_image'
        ]);

    return response()->json([
        'status' => true,
        'data' => $projects
    ]);
}

}
