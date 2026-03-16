<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\City;
use App\Models\Project;
use App\Models\ProjectUnit;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $keyword = trim((string) $request->get('keyword', $request->get('q', '')));

        if ($keyword === '') {
            return response()->json([
                'success' => true,
                'data' => [
                    'cities' => [],
                    'areas' => [],
                    'projects' => [],
                    'units' => [],
                ]
            ]);
        }

        $cities = City::query()
            ->where('name', 'LIKE', '%' . $keyword . '%')
            ->limit(8)
            ->get(['id', 'name']);

        $areas = Area::query()
            ->with('city:id,name')
            ->where('name', 'LIKE', '%' . $keyword . '%')
            ->limit(12)
            ->get(['id', 'city_id', 'name', 'slug']);

        $projects = Project::query()
            ->where('title', 'LIKE', '%' . $keyword . '%')
            ->orWhere('description', 'LIKE', '%' . $keyword . '%')
            ->orWhere('location', 'LIKE', '%' . $keyword . '%')
            ->limit(8)
            ->get();

        $units = ProjectUnit::query()
            ->where('title', 'LIKE', '%' . $keyword . '%')
            ->orWhere('type', 'LIKE', '%' . $keyword . '%')
            ->orWhere('bedrooms', 'LIKE', '%' . $keyword . '%')
            ->orWhere('bathrooms', 'LIKE', '%' . $keyword . '%')
            ->orWhere('area', 'LIKE', '%' . $keyword . '%')
            ->orWhere('price', 'LIKE', '%' . $keyword . '%')
            ->limit(8)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'cities' => $cities,
                'areas' => $areas,
                'projects' => $projects,
                'units' => $units,
            ]
        ]);
    }
}