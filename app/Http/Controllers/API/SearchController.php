<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ProjectUnit;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $keyword = $request->keyword;

        // Search in PROJECTS table
        $projects = Project::query();

        if (!empty($keyword)) {
            $projects->where('title', 'LIKE', '%' . $keyword . '%')
                     ->orWhere('description', 'LIKE', '%' . $keyword . '%')
                     ->orWhere('location', 'LIKE', '%' . $keyword . '%');
        }

        $projects = $projects->get();

        // Search in PROJECT UNITS table
        $units = ProjectUnit::query();

        if (!empty($keyword)) {
            $units->where('title', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('type', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('bedrooms', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('bathrooms', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('area', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('price', 'LIKE', '%' . $keyword . '%');
        }

        $units = $units->get();

        return response()->json([
            'success' => true,
            'projects' => $projects,
            'units' => $units,
        ]);
    }
}
