<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Project;
use App\Models\ProjectUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminProjectController extends Controller
{
    public function meta()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'cities' => City::orderBy('name')->get(['id', 'name']),
                'statuses' => [
                    ['label' => 'Draft', 'value' => 'draft'],
                    ['label' => 'Ongoing', 'value' => 'ongoing'],
                    ['label' => 'Completed', 'value' => 'completed'],
                    ['label' => 'Active', 'value' => 'active'],
                ],
                'unit_types' => [
                    'Apartment',
                    'Studio Apartment',
                    'Penthouse',
                    'Shop',
                    'Office',
                    'House',
                    'Plot',
                    'Commercial Plot',
                    'Warehouse',
                ],
            ],
        ]);
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 10);
        $perPage = $perPage < 1 ? 10 : $perPage;
        $perPage = $perPage > 50 ? 50 : $perPage;

        $search = trim((string) $request->query('search', ''));
        $cityId = $request->query('city_id');
        $status = trim((string) $request->query('status', 'all'));
        $featured = $request->query('featured');

        $query = Project::with(['city:id,name', 'units'])
            ->withCount('units');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('developer', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if (!empty($cityId)) {
            $query->where('city_id', $cityId);
        }

        if ($status !== '' && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($featured !== null && $featured !== '' && $featured !== 'all') {
            $query->where('is_featured', (int) $featured === 1 ? 1 : 0);
        }

        $projects = $query->latest()->paginate($perPage)->withQueryString();

        $projects->getCollection()->transform(function ($project) {
            $prices = $project->units->pluck('price')->filter(fn ($value) => !is_null($value));
            $areas = $project->units->pluck('area')->filter(fn ($value) => !is_null($value));
            $types = $project->units->pluck('type')->filter()->unique()->values();

            return [
                'id' => $project->id,
                'title' => $project->title,
                'slug' => $project->slug,
                'city' => optional($project->city)->name,
                'city_id' => $project->city_id,
                'location' => $project->location,
                'developer' => $project->developer,
                'description' => $project->description,
                'status' => $project->status,
                'is_featured' => (bool) $project->is_featured,
                'cover_image' => $project->cover_image,
                'cover_image_url' => $project->cover_image_url,
                'units_count' => (int) $project->units_count,
                'unit_types' => $types,
                'price_min' => $prices->count() ? (float) $prices->min() : null,
                'price_max' => $prices->count() ? (float) $prices->max() : null,
                'area_min' => $areas->count() ? (float) $areas->min() : null,
                'area_max' => $areas->count() ? (float) $areas->max() : null,
                'created_at' => optional($project->created_at)?->format('Y-m-d H:i:s'),
                'date_label' => optional($project->created_at)?->format('d M Y'),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Projects fetched successfully',
            'data' => [
                'summary' => [
                    'all' => Project::count(),
                    'draft' => Project::where('status', 'draft')->count(),
                    'ongoing' => Project::where('status', 'ongoing')->count(),
                    'completed' => Project::where('status', 'completed')->count(),
                    'active' => Project::where('status', 'active')->count(),
                    'featured' => Project::where('is_featured', 1)->count(),
                ],
                'filters' => [
                    'search' => $search,
                    'city_id' => $cityId,
                    'status' => $status,
                    'featured' => $featured,
                    'per_page' => $perPage,
                ],
                'list' => $projects->items(),
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

    public function show($id)
    {
        $project = Project::with(['city:id,name', 'units'])->find($id);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $project->id,
                'title' => $project->title,
                'slug' => $project->slug,
                'city_id' => $project->city_id,
                'location' => $project->location,
                'developer' => $project->developer,
                'description' => $project->description,
                'status' => $project->status,
                'is_featured' => (bool) $project->is_featured,
                'cover_image' => $project->cover_image,
                'cover_image_url' => $project->cover_image_url,
                'units' => $project->units->map(function ($unit) {
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
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'city_id' => 'nullable|exists:cities,id',
            'location' => 'nullable|string|max:255',
            'developer' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,ongoing,completed,active',
            'is_featured' => 'nullable',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'units' => 'nullable',
        ]);

        $units = $this->parseUnits($request);

        $project = DB::transaction(function () use ($request, $validated, $units) {
            $slug = $this->generateUniqueSlug($validated['title']);

            $coverImagePath = null;
            if ($request->hasFile('cover_image')) {
                $coverImagePath = $request->file('cover_image')->store('projects', 'public');
            }

            $project = Project::create([
                'title' => $validated['title'],
                'slug' => $slug,
                'city_id' => $validated['city_id'] ?? null,
                'location' => $validated['location'] ?? null,
                'developer' => $validated['developer'] ?? null,
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
                'is_featured' => $this->toBool($request->input('is_featured')),
                'cover_image' => $coverImagePath,
            ]);

            foreach ($units as $unit) {
                ProjectUnit::create([
                    'project_id' => $project->id,
                    'title' => $unit['title'] ?? '',
                    'type' => $unit['type'] ?? null,
                    'bedrooms' => $this->nullableInt($unit['bedrooms'] ?? null),
                    'bathrooms' => $this->nullableInt($unit['bathrooms'] ?? null),
                    'area' => $this->nullableFloat($unit['area'] ?? null),
                    'price' => $this->nullableFloat($unit['price'] ?? null),
                    'status' => $unit['status'] ?? 'available',
                ]);
            }

            return $project->load(['city:id,name', 'units']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Project created successfully',
            'data' => $project,
        ]);
    }

    public function update(Request $request, $id)
    {
        $project = Project::with('units')->find($id);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found',
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'city_id' => 'nullable|exists:cities,id',
            'location' => 'nullable|string|max:255',
            'developer' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,ongoing,completed,active',
            'is_featured' => 'nullable',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'units' => 'nullable',
        ]);

        $units = $this->parseUnits($request);

        $project = DB::transaction(function () use ($request, $validated, $project, $units) {
            $coverImagePath = $project->cover_image;

            if ($request->hasFile('cover_image')) {
                if ($project->cover_image) {
                    Storage::disk('public')->delete($project->cover_image);
                }

                $coverImagePath = $request->file('cover_image')->store('projects', 'public');
            }

            $slug = $project->slug;
            if ($project->title !== $validated['title']) {
                $slug = $this->generateUniqueSlug($validated['title'], $project->id);
            }

            $project->update([
                'title' => $validated['title'],
                'slug' => $slug,
                'city_id' => $validated['city_id'] ?? null,
                'location' => $validated['location'] ?? null,
                'developer' => $validated['developer'] ?? null,
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
                'is_featured' => $this->toBool($request->input('is_featured')),
                'cover_image' => $coverImagePath,
            ]);

            $project->units()->delete();

            foreach ($units as $unit) {
                ProjectUnit::create([
                    'project_id' => $project->id,
                    'title' => $unit['title'] ?? '',
                    'type' => $unit['type'] ?? null,
                    'bedrooms' => $this->nullableInt($unit['bedrooms'] ?? null),
                    'bathrooms' => $this->nullableInt($unit['bathrooms'] ?? null),
                    'area' => $this->nullableFloat($unit['area'] ?? null),
                    'price' => $this->nullableFloat($unit['price'] ?? null),
                    'status' => $unit['status'] ?? 'available',
                ]);
            }

            return $project->load(['city:id,name', 'units']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Project updated successfully',
            'data' => $project,
        ]);
    }

    public function destroy($id)
    {
        $project = Project::with('units')->find($id);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found',
            ], 404);
        }

        if ($project->cover_image) {
            Storage::disk('public')->delete($project->cover_image);
        }

        $project->units()->delete();
        $project->delete();

        return response()->json([
            'success' => true,
            'message' => 'Project deleted successfully',
        ]);
    }

    public function toggleFeatured($id)
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found',
            ], 404);
        }

        $project->update([
            'is_featured' => !$project->is_featured,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Project featured status updated successfully',
            'data' => [
                'id' => $project->id,
                'is_featured' => (bool) $project->is_featured,
            ],
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found',
            ], 404);
        }

        $request->validate([
            'status' => 'required|in:draft,ongoing,completed,active',
        ]);

        $project->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Project status updated successfully',
            'data' => [
                'id' => $project->id,
                'status' => $project->status,
            ],
        ]);
    }

    private function parseUnits(Request $request): array
    {
        $units = $request->input('units', []);

        if (is_string($units)) {
            $decoded = json_decode($units, true);
            $units = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($units)) {
            return [];
        }

        return collect($units)
            ->filter(fn ($unit) => is_array($unit) && !empty(trim((string) ($unit['title'] ?? ''))))
            ->values()
            ->all();
    }

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug ?: 'project';
        $counter = 1;

        while (
            Project::query()
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function toBool($value): bool
    {
        return in_array($value, [true, 1, '1', 'true', 'on', 'yes'], true);
    }

    private function nullableInt($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function nullableFloat($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float) $value;
    }
}