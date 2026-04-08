<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Society;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SocietyMapController extends Controller
{
    public function index()
    {
        $cities = City::query()
            ->withCount([
                'societies as societies_count' => function (Builder $query) {
                    $this->applyRenderableSocietyConstraint($query);
                },
            ])
            ->having('societies_count', '>', 0)
            ->orderBy('name')
            ->get(['id', 'name']);

        $featured = $this->societyListingQuery()
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
        $societies = $this->societyListingQuery()
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
            ->where(function (Builder $query) use ($slug) {
                $query->where('slug', $slug);

                if (is_numeric($slug)) {
                    $query->orWhere('id', (int) $slug);
                }
            })
            ->firstOrFail();

        $society->increment('views');

        $society = Society::with([
                'city:id,name',
                'images' => function ($q) {
                    $q->orderBy('sort_order')->orderBy('id');
                },
            ])
            ->findOrFail($society->id);

        return response()->json([
            'status' => true,
            'data' => $this->transformSocietyDetail($society),
        ]);
    }

    private function societyListingQuery(): Builder
    {
        return Society::query()
            ->with([
                'city:id,name',
                'images' => function ($q) {
                    $q->orderBy('sort_order')->orderBy('id');
                },
            ])
            ->whereHas('city')
            ->where(function (Builder $query) {
                $query->where(function (Builder $ownImageQuery) {
                    $ownImageQuery->whereNotNull('image')
                        ->where('image', '!=', '');
                })->orWhereHas('images', function (Builder $imageQuery) {
                    $imageQuery->whereNotNull('image')
                        ->where('image', '!=', '');
                });
            });
    }

    private function applyRenderableSocietyConstraint(Builder $query): void
    {
        $query->where(function (Builder $innerQuery) {
            $innerQuery->where(function (Builder $ownImageQuery) {
                $ownImageQuery->whereNotNull('image')
                    ->where('image', '!=', '');
            })->orWhereHas('images', function (Builder $imageQuery) {
                $imageQuery->whereNotNull('image')
                    ->where('image', '!=', '');
            });
        });
    }

    private function transformSocietyCard(Society $society): array
    {
        $societyMapImage = $this->resolveTypedImageUrl($society, [
            'society_map',
            'society-map',
            'map',
            'master_plan',
            'master-plan',
            'plan',
        ]);

        return [
            'id' => $society->id,
            'slug' => $society->slug,
            'name' => $society->name,
            'city_id' => $society->city_id,
            'city_name' => optional($society->city)->name,
            'description' => $society->description,
            'image' => $societyMapImage,
            'image_url' => $societyMapImage,
            'image_path' => $societyMapImage,
            'society_image' => $societyMapImage,
            'map_image' => $societyMapImage,
            'map_view_image' => null,
            'views' => (int) ($society->views ?? 0),
            'plot_finder_url' => $society->plot_finder_url,
            'map_url' => $society->map_url,
            'google_map_url' => $society->google_map_url,
            'location_url' => $society->location_url,
            'latitude' => $society->latitude,
            'longitude' => $society->longitude,
            'map_zoom' => (int) ($society->map_zoom ?? 14),
        ];
    }

    private function transformSocietyDetail(Society $society): array
    {
        $societyMapImage = $this->resolveTypedImageUrl($society, [
            'society_map',
            'society-map',
            'map',
            'master_plan',
            'master-plan',
            'plan',
        ]);

        $gallery = collect($society->images)
            ->sortBy(function ($image) {
                return sprintf(
                    '%09d-%09d',
                    (int) ($image->sort_order ?? 0),
                    (int) ($image->id ?? 0)
                );
            })
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

        return [
            'id' => $society->id,
            'slug' => $society->slug,
            'name' => $society->name,
            'city_id' => $society->city_id,
            'city_name' => optional($society->city)->name,
            'description' => $society->description,
            'views' => (int) ($society->views ?? 0),
            'cover_image' => $societyMapImage,
            'society_image' => $societyMapImage,
            'image' => $societyMapImage,
            'image_url' => $societyMapImage,
            'image_path' => $societyMapImage,
            'map_image' => $societyMapImage,
            'map_view_image' => null,
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
            'latitude' => $society->latitude,
            'longitude' => $society->longitude,
            'map_zoom' => (int) ($society->map_zoom ?? 14),
        ];
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
        $images = collect($society->images)
            ->sortBy(function ($image) {
                return sprintf(
                    '%09d-%09d',
                    (int) ($image->sort_order ?? 0),
                    (int) ($image->id ?? 0)
                );
            });

        $normalizedPreferred = collect($preferredTypes)
            ->map(fn ($type) => strtolower(str_replace('-', '_', trim((string) $type))))
            ->values();

        foreach ($normalizedPreferred as $type) {
            $match = $images->first(function ($img) use ($type) {
                $currentType = strtolower(
                    str_replace('-', '_', trim((string) ($img->type ?? '')))
                );

                return $currentType === $type && !empty($img->image);
            });

            if ($match) {
                return $match;
            }
        }

        return $images->first(fn ($img) => !empty($img->image));
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

        if (Str::startsWith($clean, 'storage/')) {
            return url($clean);
        }

        if (Storage::disk('public')->exists($clean)) {
            return url(Storage::disk('public')->url($clean));
        }

        if (file_exists(public_path($clean))) {
            return url($clean);
        }

        return url('storage/' . $clean);
    }
}