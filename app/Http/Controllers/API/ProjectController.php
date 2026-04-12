<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Project;
use App\Models\ProjectUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function meta()
    {
        $projects = Project::with(['city:id,name', 'units'])
            ->whereIn('status', ['ongoing', 'completed', 'active'])
            ->get();

        $cities = City::orderBy('name')->get(['id', 'name']);

        $developers = Project::query()
            ->whereNotNull('developer')
            ->where('developer', '!=', '')
            ->whereIn('status', ['ongoing', 'completed', 'active'])
            ->select('developer')
            ->distinct()
            ->orderBy('developer')
            ->pluck('developer')
            ->values();

        $projectTitles = Project::query()
            ->whereIn('status', ['ongoing', 'completed', 'active'])
            ->select('title')
            ->orderBy('title')
            ->pluck('title')
            ->values();

        $priceMin = ProjectUnit::min('price');
        $priceMax = ProjectUnit::max('price');
        $areaMin = ProjectUnit::min('area');
        $areaMax = ProjectUnit::max('area');

        $categories = [
            [
                'key' => 'apartment',
                'label' => 'Apartments',
                'type' => 'apartment',
                'count' => $this->countProjectsByType($projects, 'apartment'),
            ],
            [
                'key' => 'plot',
                'label' => 'Grounds',
                'type' => 'plot',
                'count' => $this->countProjectsByType($projects, 'plot'),
            ],
            [
                'key' => 'shop',
                'label' => 'Shops',
                'type' => 'shop',
                'count' => $this->countProjectsByType($projects, 'shop'),
            ],
            [
                'key' => 'house',
                'label' => 'Houses',
                'type' => 'house',
                'count' => $this->countProjectsByType($projects, 'house'),
            ],
        ];

        $featuredDevelopers = $projects
            ->filter(fn ($p) => !empty($p->developer))
            ->groupBy('developer')
            ->map(function ($developerProjects, $developerName) {
                $first = $developerProjects->first();

                return [
                    'name' => $developerName,
                    'city' => optional($first->city)->name,
                    'project_count' => $developerProjects->count(),
                    'cover_image_url' => $first->cover_image_url,
                ];
            })
            ->values()
            ->take(12);

        return response()->json([
            'success' => true,
            'message' => 'Project meta fetched successfully',
            'data' => [
                'cities' => $cities,
                'developers' => $developers,
                'project_titles' => $projectTitles,
                'price_range' => [
                    'min' => $priceMin ? (float) $priceMin : 0,
                    'max' => $priceMax ? (float) $priceMax : 0,
                ],
                'area_range' => [
                    'min' => $areaMin ? (float) $areaMin : 0,
                    'max' => $areaMax ? (float) $areaMax : 0,
                ],
                'categories' => $categories,
                'featured_developers' => $featuredDevelopers,
            ],
        ]);
    }

    public function index(Request $request)
    {
        $perPage = max((int) $request->query('per_page', 12), 1);

        $query = Project::with(['city:id,name', 'units'])
            ->whereIn('status', ['ongoing', 'completed', 'active'])
            ->latest();

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

        if ($request->filled('project_title')) {
            $query->where('title', 'like', '%' . $request->project_title . '%');
        }

        if ($request->filled('developer')) {
            $query->where('developer', 'like', '%' . $request->developer . '%');
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('featured') && $request->featured !== '' && $request->featured !== 'all') {
            $query->where('is_featured', $request->featured == '1' ? 1 : 0);
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $type = strtolower(trim((string) $request->type));

            $query->whereHas('units', function ($q) use ($type) {
                $q->where(function ($sub) use ($type) {
                    if ($type === 'apartment') {
                        $sub->whereRaw('LOWER(type) like ?', ['%apartment%'])
                            ->orWhereRaw('LOWER(type) like ?', ['%flat%'])
                            ->orWhereRaw('LOWER(type) like ?', ['%penthouse%']);
                    } elseif ($type === 'plot') {
                        $sub->whereRaw('LOWER(type) like ?', ['%plot%'])
                            ->orWhereRaw('LOWER(type) like ?', ['%land%']);
                    } elseif ($type === 'shop') {
                        $sub->whereRaw('LOWER(type) like ?', ['%shop%'])
                            ->orWhereRaw('LOWER(type) like ?', ['%commercial%']);
                    } elseif ($type === 'house') {
                        $sub->whereRaw('LOWER(type) like ?', ['%house%'])
                            ->orWhereRaw('LOWER(type) like ?', ['%villa%']);
                    } else {
                        $sub->whereRaw('LOWER(type) like ?', ['%' . $type . '%']);
                    }
                });
            });
        }

        if ($request->filled('min_price')) {
            $query->whereHas('units', function ($q) use ($request) {
                $q->where('price', '>=', $request->min_price);
            });
        }

        if ($request->filled('max_price')) {
            $query->whereHas('units', function ($q) use ($request) {
                $q->where('price', '<=', $request->max_price);
            });
        }

        if ($request->filled('min_area')) {
            $query->whereHas('units', function ($q) use ($request) {
                $q->where('area', '>=', $request->min_area);
            });
        }

        if ($request->filled('max_area')) {
            $query->whereHas('units', function ($q) use ($request) {
                $q->where('area', '<=', $request->max_area);
            });
        }

        $paginator = $query->paginate($perPage)->withQueryString();

        $list = collect($paginator->items())
            ->map(fn (Project $project) => $this->transformProjectCard($project))
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Projects fetched successfully',
            'data' => [
                'summary' => [
                    'total' => $paginator->total(),
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                ],
                'list' => $list,
            ],
        ]);
    }

    public function trending(Request $request)
    {
        $limit = max((int) $request->query('limit', 12), 1);

        $projects = Project::with(['city:id,name', 'units'])
            ->whereIn('status', ['ongoing', 'completed', 'active'])
            ->orderByDesc('is_featured')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn (Project $project) => $this->transformProjectCard($project))
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Trending projects fetched successfully',
            'data' => $projects,
        ]);
    }

    public function show($slug)
    {
        $project = Project::with(['city:id,name', 'units'])
            ->where('slug', $slug)
            ->whereIn('status', ['ongoing', 'completed', 'active'])
            ->first();

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found',
            ], 404);
        }

        $units = $project->units->map(function ($unit) {
            return [
                'id' => $unit->id,
                'title' => $unit->title,
                'type' => $unit->type,
                'bedrooms' => $unit->bedrooms,
                'bathrooms' => $unit->bathrooms,
                'area' => $unit->area,
                'price' => $unit->price,
                'status' => $unit->status,
                'price_label' => $unit->price ? 'MAD ' . number_format($unit->price, 0, '.', ',') : 'Price on request',
                'area_label' => $unit->area ? number_format($unit->area, 0, '.', ',') . ' m²' : '-',
            ];
        })->values();

        $groupedUnits = $project->units
            ->groupBy(fn ($unit) => $unit->type ?: 'Project Units')
            ->map(function ($group, $type) {
                $prices = $group->pluck('price')->filter(fn ($v) => !is_null($v));
                $areas = $group->pluck('area')->filter(fn ($v) => !is_null($v));

                return [
                    'type' => $type,
                    'count' => $group->count(),
                    'price_label' => $this->formatRangeLabel($prices->min(), $prices->max(), 'MAD '),
                    'area_label' => $this->formatRangeLabel($areas->min(), $areas->max(), '', ' m²'),
                    'units' => $group->map(function ($unit) {
                        return [
                            'id' => $unit->id,
                            'title' => $unit->title,
                            'type' => $unit->type,
                            'bedrooms' => $unit->bedrooms,
                            'bathrooms' => $unit->bathrooms,
                            'area' => $unit->area,
                            'price' => $unit->price,
                            'status' => $unit->status,
                            'price_label' => $unit->price ? 'MAD ' . number_format($unit->price, 0, '.', ',') : 'Price on request',
                            'area_label' => $unit->area ? number_format($unit->area, 0, '.', ',') . ' m²' : '-',
                        ];
                    })->values(),
                ];
            })
            ->values();

        $similarProjects = Project::with(['city:id,name', 'units'])
            ->where('id', '!=', $project->id)
            ->whereIn('status', ['ongoing', 'completed', 'active'])
            ->where(function ($q) use ($project) {
                $q->where('city_id', $project->city_id);

                if ($project->developer) {
                    $q->orWhere('developer', $project->developer);
                }
            })
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn (Project $item) => $this->transformProjectCard($item))
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Project fetched successfully',
            'data' => [
                'project' => $this->transformProjectCard($project),
                'gallery' => array_values(array_filter([
                    $project->cover_image_url,
                ])),
                'units' => $units,
                'unit_groups' => $groupedUnits,
                'similar_projects' => $similarProjects,
            ],
        ]);
    }

    private function transformProjectCard(Project $project): array
    {
        $prices = $project->units->pluck('price')->filter(fn ($v) => !is_null($v));
        $areas = $project->units->pluck('area')->filter(fn ($v) => !is_null($v));
        $types = $project->units->pluck('type')->filter()->unique()->values();

        return [
            'id' => $project->id,
            'title' => $project->title,
            'slug' => $project->slug,
            'city_id' => $project->city_id,
            'city_name' => optional($project->city)->name,
            'location' => $project->location,
            'developer' => $project->developer,
            'description' => $project->description,
            'status' => $project->status,
            'is_featured' => (bool) $project->is_featured,
            'cover_image' => $project->cover_image,
            'cover_image_url' => $project->cover_image_url,
            'unit_count' => $project->units->count(),
            'available_unit_count' => $project->units->where('status', 'available')->count(),
            'unit_types' => $types,
            'primary_unit_type' => $types->first(),
            'min_price' => $prices->count() ? (float) $prices->min() : null,
            'max_price' => $prices->count() ? (float) $prices->max() : null,
            'min_area' => $areas->count() ? (float) $areas->min() : null,
            'max_area' => $areas->count() ? (float) $areas->max() : null,
            'price_label' => $this->formatRangeLabel($prices->min(), $prices->max(), 'MAD '),
            'area_label' => $this->formatRangeLabel($areas->min(), $areas->max(), '', ' m²'),
            'created_at' => optional($project->created_at)?->format('Y-m-d H:i:s'),
        ];
    }

    private function formatRangeLabel($min, $max, string $prefix = '', string $suffix = ''): string
    {
        if (is_null($min) && is_null($max)) {
            return $prefix ? 'Price on request' : '-';
        }

        if (!is_null($min) && !is_null($max) && (float) $min === (float) $max) {
            return $prefix . number_format((float) $min, 0, '.', ',') . $suffix;
        }

        if (!is_null($min) && !is_null($max)) {
            return $prefix . number_format((float) $min, 0, '.', ',') . $suffix . ' to ' .
                $prefix . number_format((float) $max, 0, '.', ',') . $suffix;
        }

        if (!is_null($min)) {
            return $prefix . number_format((float) $min, 0, '.', ',') . $suffix;
        }

        return $prefix . number_format((float) $max, 0, '.', ',') . $suffix;
    }

    private function countProjectsByType($projects, string $typeKey): int
    {
        return $projects->filter(function ($project) use ($typeKey) {
            return $project->units->contains(function ($unit) use ($typeKey) {
                $type = strtolower((string) $unit->type);

                return match ($typeKey) {
                    'apartment' => Str::contains($type, ['apartment', 'flat', 'penthouse']),
                    'plot' => Str::contains($type, ['plot', 'land']),
                    'shop' => Str::contains($type, ['shop', 'commercial']),
                    'house' => Str::contains($type, ['house', 'villa']),
                    default => false,
                };
            });
        })->count();
    }
}