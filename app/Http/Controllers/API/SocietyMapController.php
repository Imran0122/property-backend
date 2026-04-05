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
        $cities = City::withCount('societies')
            ->having('societies_count', '>', 0)
            ->orderBy('name')
            ->get(['id', 'name']);

        $featured = Society::with([
                'city:id,name',
                'images' => function ($q) {
                    $q->orderBy('sort_order')->orderBy('id');
                },
            ])
            ->whereHas('city')
            ->orderByDesc('is_popular')
            ->orderByDesc('views')
            ->orderBy('name')
            ->take(8)
            ->get();

        return response()->json([
            'status' => true,
            'data' => [
                'cities' => $cities->map(function ($city) {
                    return [
                        'id' => $city->id,
                        'name' => $city->name,
                        'societies_count' => (int) $city->societies_count,
                    ];
                })->values(),

                'featured_societies' => $featured->map(function ($society) {
                    return $this->transformSocietyCard($society);
                })->values(),

                'societies' => $featured->map(function ($society) {
                    return $this->transformSocietyCard($society);
                })->values(),
            ],
        ]);
    }

    public function societiesByCity($id)
    {
        $societies = Society::with([
                'city:id,name',
                'images' => function ($q) {
                    $q->orderBy('sort_order')->orderBy('id');
                },
            ])
            ->where('city_id', $id)
            ->orderByDesc('is_popular')
            ->orderByDesc('views')
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $societies->map(function ($society) {
                return $this->transformSocietyCard($society);
            })->values(),
        ]);
    }

    public function show($slug)
    {
        $society = Society::with([
                'city:id,name',
                'images' => function ($q) {
                    $q->orderBy('sort_order')->orderBy('id');
                },
            ])
            ->where('slug', $slug)
            ->orWhere('id', $slug)
            ->firstOrFail();

        $society->increment('views');
        $society->refresh();

        return response()->json([
            'status' => true,
            'data' => $this->transformSocietyDetail($society),
        ]);
    }

    private function transformSocietyCard(Society $society): array
    {
        $coverImage = $this->resolveSocietyCoverImage($society);

        $societyMapImage = $this->resolveTypedImageUrl($society, [
            'society_map',
            'society-map',
            'map',
            'master_plan',
            'master-plan',
            'plan',
        ]);

        $mapViewImage = $this->resolveTypedImageUrl($society, [
            'map_view',
            'map-view',
            'view',
            'location',
            'explore',
        ]);

        return [
            'id' => $society->id,
            'slug' => $society->slug,
            'name' => $society->name,
            'city_id' => $society->city_id,
            'city_name' => optional($society->city)->name,
            'description' => $society->description,
            'image' => $coverImage,
            'image_url' => $coverImage,
            'image_path' => $coverImage,
            'society_image' => $coverImage,
            'map_image' => $societyMapImage,
            'map_view_image' => $mapViewImage,
            'views' => (int) ($society->views ?? 0),
            'plot_finder_url' => $society->plot_finder_url,
            'map_url' => $society->map_url,
            'google_map_url' => $society->google_map_url,
            'location_url' => $society->location_url,
        ];
    }

    private function transformSocietyDetail(Society $society): array
    {
        $coverImage = $this->resolveSocietyCoverImage($society);

        $societyMapImage = $this->resolveTypedImageUrl($society, [
            'society_map',
            'society-map',
            'map',
            'master_plan',
            'master-plan',
            'plan',
        ]);

        $mapViewImage = $this->resolveTypedImageUrl($society, [
            'map_view',
            'map-view',
            'view',
            'location',
            'explore',
        ]);

        $gallery = collect($society->images)
            ->map(function ($image) {
                return [
                    'id' => $image->id,
                    'type' => $image->type ?? 'gallery',
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

            'plot_finder_url' => $society->plot_finder_url,
            'map_url' => $society->map_url,
            'google_map_url' => $society->google_map_url,
            'location_url' => $society->location_url,
        ];
    }

    private function resolveSocietyCoverImage(Society $society): ?string
    {
        if (!empty($society->image)) {
            return $this->makeImageUrl($society->image);
        }

        $preferred = $this->pickImage($society, [
            'cover',
            'hero',
            'main',
            'featured',
            'thumbnail',
            'photo',
            'gallery',
            'society_map',
            'society-map',
            'map_view',
            'map-view',
        ]);

        if ($preferred && !empty($preferred->image)) {
            return $this->makeImageUrl($preferred->image);
        }

        $firstImage = collect($society->images)->sortBy('sort_order')->first();

        return $firstImage ? $this->makeImageUrl($firstImage->image) : null;
    }

    private function resolveTypedImageUrl(Society $society, array $types = []): ?string
    {
        $image = $this->pickImage($society, $types);

        if ($image && !empty($image->image)) {
            return $this->makeImageUrl($image->image);
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