<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;

class HomeController extends Controller
{
    public function projects()
    {
        return response()->json(
            Project::latest()->take(6)->get()
        );
    }
}
