<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function meta()
    {
        $projects = Project::with(['city:id,name', 'units'])->latest()->get();

        $cities = $projects
            ->filter(fn ($project) => $project->city)
            ->groupBy('city_id')
            ->map(function ($items) {
                $first = $items->first();

                return [
                    'id' => $first->city->id,
                    'name' => $first->city->name,
                    'count' => $items->count(),
                ];
            })
            ->sortBy('name')
            ->values();

        $categories = collect([
            ['key' => 'apartments', 'label' => 'Apartments'],
            ['key' => 'grounds', 'label' => 'Grounds'],
            ['key' => 'shops', 'label' => 'Shops'],
            ['key' => 'houses', 'label' => 'Houses'],
        ])->map(function ($category) use ($projects) {
            $count = $projects->filter(function ($project) use ($category) {
                return $project->units->contains(function ($unit) use ($category) {
                    return $this->resolveUnitCategory($unit->type) === $category['key'];
                });
            })->count();

            return [
                'key' => $category['key'],
                'label' => $category['label'],
                'count' => $count,
            ];
        })->values();

        $developers = $projects
            ->filter(fn ($project) => filled($project->developer))
            ->groupBy(fn ($project) => Str::lower(trim((string) $project->developer)))
            ->map(function ($items) {
                $first = $items->first();

                return [
                    'name' => $first->developer,
                    'city' => $first->city?->name,
                    'projects_count' => $items->count(),
                    'cover_image_url' => $first->cover_image_url,
                ];
            })
            ->sortByDesc('projects_count')
            ->take(10)
            ->values();

        $allUnitPrices = $projects->flatMap(fn ($project) => $project->units->pluck('price'))->filter(fn ($value) => !is_null($value));
        $allUnitAreas = $projects->flatMap(fn ($project) => $project->units->pluck('area'))->filter(fn ($value) => !is_null($value));

        return response()->json([
            'success' => true,
            'data' => [
                'cities' => $cities,
                'categories' => $categories,
                'developers' => $developers->pluck('name')->filter()->values(),
                'featured_developers' => $developers,
                'ranges' => [
                    'price' => [
                        'min' => $allUnitPrices->count() ? (float) $allUnitPrices->min() : 0,
                        'max' => $allUnitPrices->count() ? (float) $allUnitPrices->max() : 0,
                    ],
                    'area' => [
                        'min' => $allUnitAreas->count() ? (float) $allUnitAreas->min() : 0,
                        'max' => $allUnitAreas->count() ? (float) $allUnitAreas->max() : 0,
                    ],
                ],
            ],
        ]);
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 12);
        $perPage = $perPage < 1 ? 12 : $perPage;
        $perPage = $perPage > 50 ? 50 : $perPage;

        $search = trim((string) $request->query('search', ''));
        $cityId = $request->query('city_id');
        $developer = trim((string) $request->query('developer', ''));
        $status = trim((string) $request->query('status', ''));
        $category = trim((string) $request->query('category', ''));
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        $minArea = $request->query('min_area');
        $maxArea = $request->query('max_area');
        $featured = $request->query('featured');

        $query = Project::with(['city:id,name', 'units'])->latest();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('developer', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!empty($cityId)) {
            $query->where('city_id', $cityId);
        }

        if ($developer !== '') {
            $query->where('developer', 'like', "%{$developer}%");
        }

        if ($status !== '' && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($featured !== null && $featured !== '' && $featured !== 'all') {
            $query->where('is_featured', (int) $featured === 1 ? 1 : 0);
        }

        if ($category !== '') {
            $allowedTypes = $this->categoryTypes($category);

            if (!empty($allowedTypes)) {
                $query->whereHas('units', function ($q) use ($allowedTypes) {
                    foreach ($allowedTypes as $index => $type) {
                        if ($index === 0) {
                            $q->whereRaw('LOWER(type) like ?', ['%' . Str::lower($type) . '%']);
                        } else {
                            $q->orWhereRaw('LOWER(type) like ?', ['%' . Str::lower($type) . '%']);
                        }
                    }
                });
            }
        }

        if ($minPrice !== null && $minPrice !== '') {
            $query->whereHas('units', fn ($q) => $q->where('price', '>=', $minPrice));
        }

        if ($maxPrice !== null && $maxPrice !== '') {
            $query->whereHas('units', fn ($q) => $q->where('price', '<=', $maxPrice));
        }

        if ($minArea !== null && $minArea !== '') {
            $query->whereHas('units', fn ($q) => $q->where('area', '>=', $minArea));
        }

        if ($maxArea !== null && $maxArea !== '') {
            $query->whereHas('units', fn ($q) => $q->where('area', '<=', $maxArea));
        }

        $projects = $query->paginate($perPage)->withQueryString();

        $list = $projects->getCollection()->map(fn ($project) => $this->formatProjectCard($project))->values();

        return response()->json([
            'success' => true,
            'data' => [
                'list' => $list,
                'pagination' => [
                    'current_page' => $projects->currentPage(),
                    'last_page' => $projects->lastPage(),
                    'per_page' => $projects->perPage(),
                    'total' => $projects->total(),
                    'from' => $projects->firstItem(),
                    'to' => $projects->lastItem(),
                ],
            ],
        ]);
    }

    public function trending(Request $request)
    {
        $limit = (int) $request->query('limit', 8);
        $limit = $limit < 1 ? 8 : $limit;

        $projects = Project::with(['city:id,name', 'units'])
            ->orderByDesc('is_featured')
            ->latest()
            ->take($limit)
            ->get()
            ->map(fn ($project) => $this->formatProjectCard($project))
            ->values();

        return response()->json([
            'success' => true,
            'data' => $projects,
        ]);
    }

    public function show($slug)
    {
        $project = Project::with(['city:id,name', 'units'])
            ->where('slug', $slug)
            ->orWhere('id', $slug)
            ->first();

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found',
            ], 404);
        }

        $prices = $project->units->pluck('price')->filter(fn ($value) => !is_null($value));
        $areas = $project->units->pluck('area')->filter(fn ($value) => !is_null($value));

        $unitGroups = $project->units
            ->groupBy(fn ($unit) => $unit->type ?: 'Unit')
            ->map(function ($units, $type) {
                $unitPrices = $units->pluck('price')->filter(fn ($value) => !is_null($value));
                $unitAreas = $units->pluck('area')->filter(fn ($value) => !is_null($value));

                return [
                    'type' => $type,
                    'price_range_label' => $this->makeRangeLabel($unitPrices, 'MAD'),
                    'area_range_label' => $this->makeRangeLabel($unitAreas, 'm²', false),
                    'items' => $units->map(function ($unit) {
                        return [
                            'id' => $unit->id,
                            'title' => $unit->title,
                            'type' => $unit->type,
                            'bedrooms' => $unit->bedrooms,
                            'bathrooms' => $unit->bathrooms,
                            'area' => $unit->area,
                            'price' => $unit->price,
                            'status' => $unit->status,
                        ];
                    })->values(),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'project' => [
                    'id' => $project->id,
                    'title' => $project->title,
                    'slug' => $project->slug,
                    'city_name' => $project->city?->name,
                    'location' => $project->location,
                    'developer' => $project->developer,
                    'description' => $project->description,
                    'status' => $project->status,
                    'is_featured' => (bool) $project->is_featured,
                    'cover_image_url' => $project->cover_image_url,
                    'price_range_label' => $this->makeRangeLabel($prices, 'MAD'),
                    'area_range_label' => $this->makeRangeLabel($areas, 'm²', false),
                    'units_count' => $project->units->count(),
                    'created_at' => optional($project->created_at)?->format('d M Y'),
                ],
                'gallery' => array_values(array_filter([$project->cover_image_url])),
                'unit_groups' => $unitGroups,
                'overview' => [
                    'city' => $project->city?->name,
                    'developer' => $project->developer,
                    'location' => $project->location,
                    'status' => $project->status,
                    'units_count' => $project->units->count(),
                ],
                'contact' => [
                    'developer' => $project->developer,
                    'city' => $project->city?->name,
                ],
            ],
        ]);
    }

    private function formatProjectCard(Project $project): array
    {
        $prices = $project->units->pluck('price')->filter(fn ($value) => !is_null($value));
        $areas = $project->units->pluck('area')->filter(fn ($value) => !is_null($value));
        $types = $project->units->pluck('type')->filter()->unique()->values();

        return [
            'id' => $project->id,
            'title' => $project->title,
            'slug' => $project->slug,
            'city_name' => $project->city?->name,
            'location' => $project->location,
            'developer' => $project->developer,
            'description' => $project->description,
            'status' => $project->status,
            'is_featured' => (bool) $project->is_featured,
            'cover_image_url' => $project->cover_image_url,
            'units_count' => $project->units->count(),
            'type_labels' => $types,
            'price_min' => $prices->count() ? (float) $prices->min() : null,
            'price_max' => $prices->count() ? (float) $prices->max() : null,
            'area_min' => $areas->count() ? (float) $areas->min() : null,
            'area_max' => $areas->count() ? (float) $areas->max() : null,
            'price_range_label' => $this->makeRangeLabel($prices, 'MAD'),
            'area_range_label' => $this->makeRangeLabel($areas, 'm²', false),
        ];
    }

    private function makeRangeLabel(Collection $values, string $suffix = '', bool $suffixBefore = true): ?string
    {
        if ($values->isEmpty()) {
            return null;
        }

        $min = $values->min();
        $max = $values->max();

        $minText = number_format((float) $min, 0, '.', ',');
        $maxText = number_format((float) $max, 0, '.', ',');

        if ($suffixBefore) {
            return $min == $max
                ? "{$suffix} {$minText}"
                : "{$suffix} {$minText} to {$maxText}";
        }

        return $min == $max
            ? "{$minText} {$suffix}"
            : "{$minText} to {$maxText} {$suffix}";
    }

    private function resolveUnitCategory(?string $type): string
    {
        $type = Str::lower(trim((string) $type));

        if (
            str_contains($type, 'apartment') ||
            str_contains($type, 'studio') ||
            str_contains($type, 'penthouse') ||
            str_contains($type, 'flat')
        ) {
            return 'apartments';
        }

        if (
            str_contains($type, 'plot') ||
            str_contains($type, 'ground') ||
            str_contains($type, 'land')
        ) {
            return 'grounds';
        }

        if (str_contains($type, 'shop')) {
            return 'shops';
        }

        if (
            str_contains($type, 'house') ||
            str_contains($type, 'villa')
        ) {
            return 'houses';
        }

        return 'apartments';
    }

    private function categoryTypes(string $category): array
    {
        return match ($category) {
            'apartments' => ['apartment', 'studio', 'penthouse', 'flat'],
            'grounds' => ['plot', 'ground', 'land'],
            'shops' => ['shop'],
            'houses' => ['house', 'villa'],
            default => [],
        };
    }
}