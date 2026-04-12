<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function meta()
    {
        $projects = Project::with(['city:id,name', 'units'])->latest()->get();

        $cities = $projects
            ->filter(fn($project) => $project->city)
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
            ['key' => 'apartments', 'label' => 'Appartements'],
            ['key' => 'grounds', 'label' => 'Terrains'],
            ['key' => 'shops', 'label' => 'Boutiques'],
            ['key' => 'houses', 'label' => 'Maisons'],
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
            ->filter(fn($project) => filled($project->developer))
            ->groupBy(fn($project) => Str::lower(trim((string) $project->developer)))
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
            ->values();

        $allPrices = $projects->flatMap(fn($project) => $project->units->pluck('price'))->filter();
        $allAreas = $projects->flatMap(fn($project) => $project->units->pluck('area'))->filter();

        return response()->json([
            'success' => true,
            'data' => [
                'cities' => $cities,
                'categories' => $categories,
                'developers' => $developers->pluck('name')->values(),
                'featured_developers' => $developers->take(10)->values(),
                'ranges' => [
                    'price' => [
                        'min' => $allPrices->count() ? (float) $allPrices->min() : 0,
                        'max' => $allPrices->count() ? (float) $allPrices->max() : 0,
                    ],
                    'area' => [
                        'min' => $allAreas->count() ? (float) $allAreas->min() : 0,
                        'max' => $allAreas->count() ? (float) $allAreas->max() : 0,
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

        $query = Project::with(['city:id,name', 'units'])->latest();

        if ($request->filled('search')) {
            $search = trim((string) $request->search);

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('developer', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        if ($request->filled('developer')) {
            $developer = trim((string) $request->developer);
            $query->where('developer', 'like', "%{$developer}%");
        }

        if ($request->filled('featured')) {
            $featured = $request->featured;
            if ($featured === '1' || $featured === 1) {
                $query->where('is_featured', 1);
            }
            if ($featured === '0' || $featured === 0) {
                $query->where('is_featured', 0);
            }
        }

        if ($request->filled('category')) {
            $types = $this->categoryTypes($request->category);

            if (!empty($types)) {
                $query->whereHas('units', function ($q) use ($types) {
                    $q->where(function ($sub) use ($types) {
                        foreach ($types as $index => $type) {
                            if ($index === 0) {
                                $sub->whereRaw('LOWER(type) LIKE ?', ['%' . Str::lower($type) . '%']);
                            } else {
                                $sub->orWhereRaw('LOWER(type) LIKE ?', ['%' . Str::lower($type) . '%']);
                            }
                        }
                    });
                });
            }
        }

        if ($request->filled('min_price')) {
            $minPrice = (float) $request->min_price;
            $query->whereHas('units', fn($q) => $q->where('price', '>=', $minPrice));
        }

        if ($request->filled('max_price')) {
            $maxPrice = (float) $request->max_price;
            $query->whereHas('units', fn($q) => $q->where('price', '<=', $maxPrice));
        }

        if ($request->filled('min_area')) {
            $minArea = (float) $request->min_area;
            $query->whereHas('units', fn($q) => $q->where('area', '>=', $minArea));
        }

        if ($request->filled('max_area')) {
            $maxArea = (float) $request->max_area;
            $query->whereHas('units', fn($q) => $q->where('area', '<=', $maxArea));
        }

        $projects = $query->paginate($perPage)->withQueryString();

        $list = $projects->getCollection()
            ->map(fn($project) => $this->formatProjectCard($project))
            ->values();

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
        $limit = (int) $request->query('limit', 10);
        $limit = $limit < 1 ? 10 : $limit;

        $projects = Project::with(['city:id,name', 'units'])
            ->orderByDesc('is_featured')
            ->latest()
            ->take($limit)
            ->get()
            ->map(fn($project) => $this->formatProjectCard($project))
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

        $prices = $project->units->pluck('price')->filter();
        $areas = $project->units->pluck('area')->filter();

        $unitGroups = $project->units
            ->groupBy(fn($unit) => $unit->type ?: 'Unit')
            ->map(function ($units, $type) {
                $unitPrices = $units->pluck('price')->filter();
                $unitAreas = $units->pluck('area')->filter();

                return [
                    'type' => $type,
                    'count' => $units->count(),
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
            ],
        ]);
    }

    private function formatProjectCard(Project $project): array
    {
        $prices = $project->units->pluck('price')->filter();
        $areas = $project->units->pluck('area')->filter();
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
            'price_range_label' => $this->makeRangeLabel($prices, 'MAD'),
            'area_range_label' => $this->makeRangeLabel($areas, 'm²', false),
        ];
    }

    private function makeRangeLabel(Collection $values, string $suffix = '', bool $prefix = true): ?string
    {
        if ($values->isEmpty()) {
            return null;
        }

        $min = number_format((float) $values->min(), 0, '.', ',');
        $max = number_format((float) $values->max(), 0, '.', ',');

        if ($prefix) {
            return $min === $max ? "{$suffix} {$min}" : "{$suffix} {$min} to {$max}";
        }

        return $min === $max ? "{$min} {$suffix}" : "{$min} to {$max} {$suffix}";
    }

    private function resolveUnitCategory(?string $type): string
    {
        $type = Str::lower(trim((string) $type));

        if (
            str_contains($type, 'apartment') ||
            str_contains($type, 'studio') ||
            str_contains($type, 'flat') ||
            str_contains($type, 'penthouse')
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
            'apartments' => ['apartment', 'studio', 'flat', 'penthouse'],
            'grounds' => ['plot', 'ground', 'land'],
            'shops' => ['shop'],
            'houses' => ['house', 'villa'],
            default => [],
        };
    }
}