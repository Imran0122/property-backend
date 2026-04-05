<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Society;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SocietyMapController extends Controller
{
    public function index()
    {
        $citiesWithCounts = City::withCount('societies')
            ->having('societies_count', '>', 0)
            ->orderBy('name')
            ->get(['id', 'name']);

        $popularFeatured = Society::with(['city:id,name', 'images'])
            ->where('is_popular', 1)
            ->orderByDesc('views')
            ->orderBy('name')
            ->take(4)
            ->get();

        if ($popularFeatured->count() < 4) {
            $excludeIds = $popularFeatured->pluck('id')->all();

            $additional = Society::with(['city:id,name', 'images'])
                ->when(!empty($excludeIds), function ($q) use ($excludeIds) {
                    $q->whereNotIn('id', $excludeIds);
                })
                ->orderByDesc('views')
                ->orderBy('name')
                ->take(4 - $popularFeatured->count())
                ->get();

            $popularFeatured = $popularFeatured->concat($additional)->values();
        }

        $allPreviewSocieties = Society::with(['city:id,name', 'images'])
            ->orderByDesc('views')
            ->orderBy('name')
            ->take(20)
            ->get();

        return response()->json([
            'status' => true,
            'data' => [
                'cities' => $citiesWithCounts->map(fn ($city) => [
                    'id' => $city->id,
                    'name' => $city->name,
                    'societies_count' => (int) $city->societies_count,
                ])->values(),
                'cities_with_counts' => $citiesWithCounts->map(fn ($city) => [
                    'id' => $city->id,
                    'name' => $city->name,
                    'societies_count' => (int) $city->societies_count,
                ])->values(),
                'featured_societies' => $popularFeatured
                    ->map(fn ($society) => $this->transformSocietyCard($society))
                    ->values(),
                'societies' => $allPreviewSocieties
                    ->map(fn ($society) => $this->transformSocietyCard($society))
                    ->values(),
            ],
        ]);
    }

    public function societiesByCity($id)
    {
        $societies = Society::with(['city:id,name', 'images'])
            ->where('city_id', $id)
            ->orderByDesc('views')
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $societies
                ->map(fn ($society) => $this->transformSocietyCard($society))
                ->values(),
        ]);
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
                'message' => 'Society map not found',
            ], 404);
        }

        $society->increment('views');

        return response()->json([
            'status' => true,
            'data' => $this->transformSocietyDetail($society->fresh(['city:id,name', 'images'])),
        ]);
    }

    private function transformSocietyCard(Society $society): array
    {
        $coverImage = $this->resolveSocietyCoverImage($society);
        $mapImage = $this->resolveSocietyMapImage($society);

        return [
            'id' => $society->id,
            'society_id' => $society->id,
            'slug' => $society->slug,
            'name' => $society->name,
            'society_name' => $society->name,
            'city_id' => $society->city_id,
            'city_name' => optional($society->city)->name,
            'image' => $coverImage,
            'image_url' => $coverImage,
            'image_path' => $coverImage,
            'thumbnail' => $coverImage,
            'photo' => $coverImage,
            'map_image' => $mapImage,
            'views' => (int) ($society->views ?? 0),
        ];
    }

    private function transformSocietyDetail(Society $society): array
    {
        $coverImage = $this->resolveSocietyCoverImage($society);
        $societyMapImage = $this->resolveSocietyMapImage($society);
        $mapViewImage = $this->resolveMapViewImage($society);

        $gallery = collect($society->images)
            ->sortBy('sort_order')
            ->map(function ($image) {
                return [
                    'id' => $image->id,
                    'type' => $image->type ?? 'society_map',
                    'title' => $image->title,
                    'sort_order' => (int) ($image->sort_order ?? 0),
                    'image' => $this->makeImageUrl($image->image),
                ];
            })
            ->filter(fn ($item) => !empty($item['image']))
            ->values();

        if ($gallery->isEmpty() && $coverImage) {
            $gallery = collect([
                [
                    'id' => null,
                    'type' => 'cover',
                    'title' => $society->name,
                    'sort_order' => 0,
                    'image' => $coverImage,
                ],
            ]);
        }

        return [
            'id' => $society->id,
            'slug' => $society->slug,
            'name' => $society->name,
            'city_id' => $society->city_id,
            'city_name' => optional($society->city)->name,
            'description' => $society->description,
            'views' => (int) ($society->views ?? 0),
            'society_image' => $coverImage,
            'image' => $coverImage,
            'image_url' => $coverImage,
            'image_path' => $coverImage,
            'map_image' => $societyMapImage,
            'map_view_image' => $mapViewImage,
            'gallery' => $gallery,
            'external_map_url' =>
                $society->plot_finder_url
                ?: $society->map_url
                ?: $society->google_map_url
                ?: $society->location_url,
        ];
    }

    private function resolveSocietyCoverImage(Society $society): ?string
    {
        if (!empty($society->image)) {
            return $this->makeImageUrl($society->image);
        }

        $coverImageModel = $this->pickImage($society, [
            'cover',
            'hero',
            'main',
            'featured',
            'thumbnail',
            'photo',
            'society',
            'society_cover',
            'society-cover',
            'map_view',
            'map-view',
            'view',
        ]);

        if ($coverImageModel && !empty($coverImageModel->image)) {
            return $this->makeImageUrl($coverImageModel->image);
        }

        $firstImage = collect($society->images)->sortBy('sort_order')->first();

        return $firstImage ? $this->makeImageUrl($firstImage->image) : null;
    }

    private function resolveSocietyMapImage(Society $society): ?string
    {
        $mapImageModel = $this->pickImage($society, [
            'society_map',
            'society-map',
            'map',
            'master_plan',
            'master-plan',
            'plan',
        ]);

        if ($mapImageModel && !empty($mapImageModel->image)) {
            return $this->makeImageUrl($mapImageModel->image);
        }

        return null;
    }

    private function resolveMapViewImage(Society $society): ?string
    {
        $mapViewModel = $this->pickImage($society, [
            'map_view',
            'map-view',
            'view',
            'location_map',
            'location-map',
        ]);

        if ($mapViewModel && !empty($mapViewModel->image)) {
            return $this->makeImageUrl($mapViewModel->image);
        }

        return null;
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

        return null;
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

        if (Str::startsWith($clean, 'public/')) {
            $clean = Str::after($clean, 'public/');
        }

        $baseUrl = rtrim(config('app.url') ?: request()->getSchemeAndHttpHost(), '/');

        if (Str::startsWith($clean, 'storage/')) {
            return $baseUrl . '/' . $clean;
        }

        if (Storage::disk('public')->exists($clean)) {
            return $baseUrl . Storage::disk('public')->url($clean);
        }

        if (file_exists(public_path($clean))) {
            return $baseUrl . '/' . $clean;
        }

        return $baseUrl . '/storage/' . $clean;
    }
}