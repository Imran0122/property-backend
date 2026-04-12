<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

        $featuredFilter = $request->get('featured_only', $request->get('featured', $request->get('is_featured')));

        if ($featuredFilter !== null && $featuredFilter !== '' && $featuredFilter !== 'all') {
            if ($this->isTruthy($featuredFilter)) {
                $query->where('is_featured', 1);
            } elseif (in_array($featuredFilter, [0, '0', false, 'false', 'no', 'off'], true)) {
                $query->where('is_featured', 0);
            }
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
        $coverImageUrl = $this->getPrimaryProjectImageUrl($project);

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
            'cover_image_url' => $coverImageUrl,
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
        $coverImageUrl = $this->getPrimaryProjectImageUrl($project);
        $gallery = $this->getProjectGallery($project->id);

        if (empty($gallery) && $coverImageUrl) {
            $gallery = [[
                'id' => 'cover',
                'image_path' => $project->cover_image,
                'image_url' => $coverImageUrl,
                'is_primary' => true,
            ]];
        }

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
            'cover_image_url' => $coverImageUrl,
            'gallery' => $gallery,
            'images' => $gallery,
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

    private function getPrimaryProjectImageUrl(Project $project): ?string
    {
        $gallery = $this->getProjectGallery($project->id);

        if (!empty($gallery) && !empty($gallery[0]['image_url'])) {
            return $gallery[0]['image_url'];
        }

        return $this->normalizeImage($project->cover_image);
    }

    private function getProjectGallery(int $projectId): array
    {
        if (!Schema::hasTable('project_images')) {
            return [];
        }

        $imageColumn = $this->projectImageColumn();

        if (!$imageColumn) {
            return [];
        }

        $query = DB::table('project_images')->where('project_id', $projectId);

        if (Schema::hasColumn('project_images', 'is_primary')) {
            $query->orderByDesc('is_primary');
        }

        if (Schema::hasColumn('project_images', 'sort_order')) {
            $query->orderBy('sort_order');
        } else {
            $query->orderBy('id');
        }

        return $query->get()->map(function ($row) use ($imageColumn) {
            $path = $row->{$imageColumn} ?? null;

            return [
                'id' => $row->id,
                'image_path' => $path,
                'image_url' => $this->normalizeImage($path),
                'is_primary' => (bool) ($row->is_primary ?? false),
            ];
        })->values()->toArray();
    }

    private function projectImageColumn(): ?string
    {
        if (!Schema::hasTable('project_images')) {
            return null;
        }

        if (Schema::hasColumn('project_images', 'image_path')) {
            return 'image_path';
        }

        if (Schema::hasColumn('project_images', 'image')) {
            return 'image';
        }

        return null;
    }

    private function normalizeImage(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $path = str_replace('\\', '/', trim($path));

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return preg_replace('/^http:\/\//i', 'https://', $path);
        }

        $clean = ltrim($path, '/');

        if (Str::startsWith($clean, 'storage/')) {
            return url($clean);
        }

        return url('storage/' . $clean);
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
        return rtrim(rtrim($formatted, '0'), '.');
    }

    private function isTruthy($value): bool
    {
        return in_array($value, [true, 1, '1', 'true', 'yes', 'on'], true);
    }
}