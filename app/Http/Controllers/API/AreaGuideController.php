<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Area;
use App\Models\Society;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AreaGuideController extends Controller
{
    public function index()
    {
        $cities = City::select('id', 'name')->orderBy('name')->get();

        $data = [];

        foreach ($cities as $city) {
            $popular = Society::with(['city:id,name', 'images'])
                ->where('city_id', $city->id)
                ->where('is_popular', 1)
                ->get()
                ->map(fn($society) => $this->transformSocietyCard($society))
                ->values();

            $links = Society::with(['city:id,name', 'images'])
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
        $society = Society::with(['city:id,name', 'images'])
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
        // Main / card image always prefer societies.image
        $coverImage = $this->makeImageUrl($society->image);

        // Optional map image from society_images table
        $mapImageModel = $this->pickImage($society, [
            'society_map',
            'society-map',
            'map',
            'master_plan',
            'master-plan',
            'plan',
        ]);

        $mapImage = $mapImageModel ? $this->makeImageUrl($mapImageModel->image) : null;

        return [
            'id' => $society->id,
            'slug' => $society->slug,
            'name' => $society->name,
            'city_name' => optional($society->city)->name,
            'description' => $society->description,
            'image' => $coverImage,
            'image_path' => $coverImage,
            'map_image' => $mapImage,
            'views' => (int) ($society->views ?? 0),
        ];
    }

    private function transformSocietyDetail(Society $society): array
    {
        $coverImage = $this->makeImageUrl($society->image);

        $mapImageModel = $this->pickImage($society, [
            'society_map',
            'society-map',
            'map',
            'master_plan',
            'master-plan',
            'plan',
        ]);

        $mapImage = $mapImageModel ? $this->makeImageUrl($mapImageModel->image) : null;

        $gallery = collect($society->images)->map(function ($image) {
            return [
                'id' => $image->id,
                'image' => $this->makeImageUrl($image->image),
                'type' => $image->type,
                'title' => $image->title,
                'sort_order' => (int) $image->sort_order,
            ];
        })->values();

        // If no gallery image exists, at least add the main society image
        if ($gallery->isEmpty() && $coverImage) {
            $gallery = collect([
                [
                    'id' => null,
                    'image' => $coverImage,
                    'type' => 'cover',
                    'title' => $society->name,
                    'sort_order' => 0,
                ]
            ]);
        }

        return [
            'id' => $society->id,
            'slug' => $society->slug,
            'name' => $society->name,
            'city_name' => optional($society->city)->name,
            'description' => $society->description,
            'society_image' => $coverImage,
            'map_image' => $mapImage,
            'gallery' => $gallery->values(),
            'external_map_url' =>
                $society->plot_finder_url ??
                $society->map_url ??
                $society->google_map_url ??
                $society->location_url,
        ];
    }

    private function pickImage(Society $society, array $preferredTypes = [])
    {
        $images = collect($society->images);

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

    private function makeImageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $clean = trim(str_replace('\\', '/', $path));

        if (Str::startsWith($clean, ['http://', 'https://'])) {
            return $clean;
        }

        $clean = ltrim($clean, '/');

        // If DB saved "public/..." convert to storage path
        if (Str::startsWith($clean, 'public/')) {
            $clean = Str::after($clean, 'public/');
        }

        // If already starts with storage/
        if (Str::startsWith($clean, 'storage/')) {
            return url($clean);
        }

        // First try file in storage/app/public
        if (Storage::disk('public')->exists($clean)) {
            return Storage::disk('public')->url($clean);
        }

        // Fallback if file is directly under public folder like /societies/...
        return url($clean);
    }
}