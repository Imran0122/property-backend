<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::query()
            ->with([
                'city:id,name',
                'units:id,project_id,title,type,bedrooms,bathrooms,area,price,status',
            ])
            ->where('status', '!=', 'draft');

        if ($request->filled('slug')) {
            $project = (clone $query)
                ->where('slug', $request->slug)
                ->first();

            if (!$project) {
                return response()->json([
                    'status' => false,
                    'message' => 'Project not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $this->formatDetail($project),
            ]);
        }

        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        if ($request->filled('city')) {
            $city = trim((string) $request->city);
            $query->where(function ($q) use ($city) {
                $q->whereHas('city', function ($cityQuery) use ($city) {
                    $cityQuery->where('name', 'like', '%' . $city . '%');
                })->orWhere('location', 'like', '%' . $city . '%');
            });
        }

        if ($request->filled('project_title')) {
            $query->where('title', 'like', '%' . trim((string) $request->project_title) . '%');
        }

        if ($request->filled('developer')) {
            $query->where('developer', 'like', '%' . trim((string) $request->developer) . '%');
        }

        if ($this->isTruthy($request->featured_only)) {
            $query->where('is_featured', 1);
        }

        $propertyType = trim((string) $request->get('property_type', ''));
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');
        $minArea = $request->get('min_area');
        $maxArea = $request->get('max_area');

        if (
            $propertyType !== '' ||
            $minPrice !== null ||
            $maxPrice !== null ||
            $minArea !== null ||
            $maxArea !== null
        ) {
            $query->whereHas('units', function ($unitQuery) use ($propertyType, $minPrice, $maxPrice, $minArea, $maxArea) {
                if ($propertyType !== '' && strtolower($propertyType) !== 'all') {
                    $unitQuery->where('type', 'like', '%' . $propertyType . '%');
                }

                if ($minPrice !== null && $minPrice !== '') {
                    $unitQuery->where('price', '>=', (float) $minPrice);
                }

                if ($maxPrice !== null && $maxPrice !== '') {
                    $unitQuery->where('price', '<=', (float) $maxPrice);
                }

                if ($minArea !== null && $minArea !== '') {
                    $unitQuery->where('area', '>=', (float) $minArea);
                }

                if ($maxArea !== null && $maxArea !== '') {
                    $unitQuery->where('area', '<=', (float) $maxArea);
                }
            });
        }

        $perPage = max((int) $request->get('per_page', 12), 1);

        $projects = $query
            ->orderByDesc('is_featured')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'status' => true,
            'data' => collect($projects->items())
                ->map(fn ($project) => $this->formatCard($project))
                ->values(),
            'meta' => [
                'current_page' => $projects->currentPage(),
                'last_page' => $projects->lastPage(),
                'per_page' => $projects->perPage(),
                'total' => $projects->total(),
            ],
        ]);
    }

    public function trending(Request $request)
    {
        $limit = max((int) $request->get('limit', 8), 1);

        $projects = Project::query()
            ->with([
                'city:id,name',
                'units:id,project_id,title,type,bedrooms,bathrooms,area,price,status',
            ])
            ->where('status', '!=', 'draft')
            ->orderByDesc('is_featured')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $projects->map(fn ($project) => $this->formatCard($project))->values(),
        ]);
    }

    private function formatCard(Project $project): array
    {
        $summary = $this->buildSummary($project);

        return [
            'id' => $project->id,
            'title' => $project->title,
            'slug' => $project->slug,
            'city' => optional($project->city)->name,
            'location' => $project->location ?: optional($project->city)->name,
            'developer' => $project->developer,
            'description' => $project->description,
            'status' => $project->status,
            'is_featured' => (bool) $project->is_featured,
            'cover_image' => $project->cover_image,
            'cover_image_url' => $this->normalizeImage($project->cover_image),
            'price_min' => $summary['price_min'],
            'price_max' => $summary['price_max'],
            'price_label' => $summary['price_label'],
            'area_min' => $summary['area_min'],
            'area_max' => $summary['area_max'],
            'area_label' => $summary['area_label'],
            'unit_types' => $summary['unit_types'],
            'unit_types_label' => $summary['unit_types_label'],
            'available_units' => $summary['available_units'],
            'units_count' => $project->units->count(),
        ];
    }

    private function formatDetail(Project $project): array
    {
        $summary = $this->buildSummary($project);
        $image = $this->normalizeImage($project->cover_image);

        return [
            'id' => $project->id,
            'title' => $project->title,
            'slug' => $project->slug,
            'city' => optional($project->city)->name,
            'location' => $project->location ?: optional($project->city)->name,
            'developer' => $project->developer,
            'description' => $project->description,
            'status' => $project->status,
            'is_featured' => (bool) $project->is_featured,
            'cover_image' => $project->cover_image,
            'cover_image_url' => $image,
            'gallery' => array_values(array_filter([$image])),
            'price_min' => $summary['price_min'],
            'price_max' => $summary['price_max'],
            'price_label' => $summary['price_label'],
            'area_min' => $summary['area_min'],
            'area_max' => $summary['area_max'],
            'area_label' => $summary['area_label'],
            'unit_types' => $summary['unit_types'],
            'unit_types_label' => $summary['unit_types_label'],
            'available_units' => $summary['available_units'],
            'units_count' => $project->units->count(),
            'units' => $project->units->map(function ($unit) {
                return [
                    'id' => $unit->id,
                    'title' => $unit->title,
                    'type' => $unit->type,
                    'bedrooms' => $unit->bedrooms,
                    'bathrooms' => $unit->bathrooms,
                    'area' => $unit->area !== null ? (float) $unit->area : null,
                    'price' => $unit->price !== null ? (float) $unit->price : null,
                    'status' => $unit->status,
                ];
            })->values(),
        ];
    }

    private function buildSummary(Project $project): array
    {
        $units = $project->units ?? collect();

        $prices = $units->pluck('price')
            ->filter(fn ($value) => $value !== null && $value !== '');

        $areas = $units->pluck('area')
            ->filter(fn ($value) => $value !== null && $value !== '');

        $unitTypes = $units->pluck('type')
            ->filter()
            ->map(fn ($type) => trim((string) $type))
            ->unique()
            ->values();

        $availableUnits = $units->filter(function ($unit) {
            return strtolower((string) $unit->status) !== 'sold';
        })->count();

        $priceMin = $prices->isNotEmpty() ? (float) $prices->min() : null;
        $priceMax = $prices->isNotEmpty() ? (float) $prices->max() : null;
        $areaMin = $areas->isNotEmpty() ? (float) $areas->min() : null;
        $areaMax = $areas->isNotEmpty() ? (float) $areas->max() : null;

        return [
            'price_min' => $priceMin,
            'price_max' => $priceMax,
            'price_label' => $this->makeMoneyLabel($priceMin, $priceMax),
            'area_min' => $areaMin,
            'area_max' => $areaMax,
            'area_label' => $this->makeAreaLabel($areaMin, $areaMax),
            'unit_types' => $unitTypes->all(),
            'unit_types_label' => $unitTypes->isNotEmpty()
                ? $unitTypes->implode(', ')
                : 'Project Units',
            'available_units' => $availableUnits,
        ];
    }

    private function normalizeImage(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $path = trim($path);

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $base = rtrim(url('/'), '/');
        $path = ltrim($path, '/');

        if (Str::startsWith($path, 'storage/')) {
            return $base . '/' . $path;
        }

        if (Str::startsWith($path, 'projects/')) {
            return $base . '/storage/' . $path;
        }

        return $base . '/storage/projects/' . $path;
    }

    private function makeMoneyLabel(?float $min, ?float $max): string
    {
        if ($min === null && $max === null) {
            return 'Price on request';
        }

        if ($min !== null && $max !== null && $min === $max) {
            return 'MAD ' . $this->formatNumber($min);
        }

        if ($min !== null && $max !== null) {
            return 'MAD ' . $this->formatNumber($min) . ' to ' . $this->formatNumber($max);
        }

        if ($min !== null) {
            return 'From MAD ' . $this->formatNumber($min);
        }

        return 'Up to MAD ' . $this->formatNumber((float) $max);
    }

    private function makeAreaLabel(?float $min, ?float $max): string
    {
        if ($min === null && $max === null) {
            return '-';
        }

        if ($min !== null && $max !== null && $min === $max) {
            return $this->formatNumber($min) . ' m²';
        }

        if ($min !== null && $max !== null) {
            return $this->formatNumber($min) . ' m² to ' . $this->formatNumber($max) . ' m²';
        }

        if ($min !== null) {
            return 'From ' . $this->formatNumber($min) . ' m²';
        }

        return 'Up to ' . $this->formatNumber((float) $max) . ' m²';
    }

    private function formatNumber(float $value): string
    {
        $formatted = number_format($value, 2, '.', ',');
        $formatted = rtrim(rtrim($formatted, '0'), '.');

        return $formatted;
    }

    private function isTruthy($value): bool
    {
        return in_array($value, [true, 1, '1', 'true', 'yes', 'on'], true);
    }
}