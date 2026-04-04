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
        $cities = City::select('id', 'name')->orderBy('name')->get();

        $data = [];

        foreach ($cities as $city) {
            $popular = Society::with([
                    'city:id,name',
                    'images:id,society_id,image,type,title,sort_order'
                ])
                ->where('city_id', $city->id)
                ->where('is_popular', 1)
                ->get()
                ->map(fn($society) => $this->transformSocietyCard($society))
                ->values();

            $links = Society::with([
                    'city:id,name',
                    'images:id,society_id,image,type,title,sort_order'
                ])
                ->where('city_id', $city->id)
                ->get()
                ->map(fn($society) => $this->transformSocietyCard($society))
                ->values();

            if ($popular->isNotEmpty() || $links->isNotEmpty()) {
                $data[$city->name] = [
                    'popular' => $popular,
                    'links' => $links,
                ];
            }
        }

        return response()->json($data);
    }

    public function show($slug)
    {
        $society = Society::with([
                'city:id,name',
                'images:id,society_id,image,type,title,sort_order'
            ])
            ->where('slug', $slug)
            ->orWhere('id', $slug)
            ->first();

        if (!$society) {
            return response()->json([
                'status' => false,
                'message' => 'Area guide not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $this->transformSocietyDetail($society),
        ]);
    }

    public function mostViewed()
    {
        $cities = City::all();
        $data = [];

        foreach ($cities as $city) {
            $areas = Area::where('city_id', $city->id)
                ->orderBy('views', 'desc')
                ->take(6)
                ->get(['name', 'slug', 'views']);

            if ($areas->count()) {
                $data[$city->name] = $areas;
            }
        }

        return response()->json($data);
    }

    public function searchCities(Request $request)
    {
        $search = $request->search;

        $cities = City::where('name', 'LIKE', "%$search%")
            ->get(['id', 'name']);

        return response()->json($cities);
    }

    private function transformSocietyCard(Society $society): array
    {
        $societyImage = $this->pickImage($society, [
            'society',
            'cover',
            'thumbnail',
            'hero',
            'main',
            'featured',
        ]);

        $mapImage = $this->pickImage($society, [
            'map',
            'society-map',
            'master-plan',
            'plan',
        ]);

        return [
            'id' => $society->id,
            'slug' => $society->slug,
            'name' => $society->name,
            'city_name' => optional($society->city)->name,
            'description' => $society->description,
            'image' => $societyImage?->image,
            'image_path' => $societyImage?->image,
            'map_image' => $mapImage?->image,
            'views' => (int) ($society->views ?? 0),
        ];
    }

    private function transformSocietyDetail(Society $society): array
    {
        $societyImage = $this->pickImage($society, [
            'society',
            'cover',
            'thumbnail',
            'hero',
            'main',
            'featured',
        ]);

        $mapImage = $this->pickImage($society, [
            'map',
            'society-map',
            'master-plan',
            'plan',
        ]);

        $gallery = $society->images->map(function ($image) {
            return [
                'id' => $image->id,
                'image' => $image->image,
                'type' => $image->type,
                'title' => $image->title,
                'sort_order' => (int) $image->sort_order,
            ];
        })->values();

        return [
            'id' => $society->id,
            'slug' => $society->slug,
            'name' => $society->name,
            'city_name' => optional($society->city)->name,
            'description' => $society->description,
            'society_image' => $societyImage?->image,
            'map_image' => $mapImage?->image,
            'gallery' => $gallery,
            'external_map_url' =>
                $society->plot_finder_url ??
                $society->map_url ??
                $society->google_map_url ??
                $society->location_url,
        ];
    }

    private function pickImage(Society $society, array $preferredTypes = [])
    {
        $images = $society->images ?? collect();

        foreach ($preferredTypes as $type) {
            $match = $images->first(function ($img) use ($type) {
                return strtolower((string) $img->type) === strtolower($type);
            });

            if ($match) {
                return $match;
            }
        }

        return $images->sortBy('sort_order')->first();
    }
}