<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminProjectController extends Controller
{
    public function meta()
    {
        $cities = City::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $staticUnitTypes = [
            'Apartment',
            'Penthouse',
            'House',
            'Shop',
            'Office',
            'Plot',
            'Commercial Plot',
        ];

        $dynamicUnitTypes = [];

        if (Schema::hasTable('project_units') && Schema::hasColumn('project_units', 'type')) {
            $dynamicUnitTypes = DB::table('project_units')
                ->whereNotNull('type')
                ->where('type', '!=', '')
                ->distinct()
                ->orderBy('type')
                ->pluck('type')
                ->toArray();
        }

        $unitTypes = collect(array_merge($staticUnitTypes, $dynamicUnitTypes))
            ->filter()
            ->unique()
            ->values()
            ->map(fn ($type) => [
                'label' => $type,
                'value' => $type,
            ]);

        return response()->json([
            'status' => true,
            'data' => [
                'cities' => $cities,
                'unit_types' => $unitTypes,
            ],
        ]);
    }

    public function index(Request $request)
    {
        $query = Project::query()
            ->with([
                'city:id,name',
                'units:id,project_id,price',
            ]);

        $search = trim((string) $request->get('search', ''));
        $cityId = $request->get('city_id');
        $status = $request->get('status');
        $featured = $request->get('featured');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('developer', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!empty($cityId) && $cityId !== 'all') {
            $query->where('city_id', $cityId);
        }

        if (!empty($status) && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($featured === '0' || $featured === '1') {
            $query->where('is_featured', (int) $featured);
        }

        $perPage = (int) $request->get('per_page', 10);
        $perPage = $perPage > 0 ? min($perPage, 50) : 10;

        $projects = $query->latest()->paginate($perPage);

        $list = collect($projects->items())->map(function ($project) {
            $prices = $project->units
                ->pluck('price')
                ->filter(fn ($price) => $price !== null && $price !== '');

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
                'units_count' => $project->units->count(),
                'price_min' => $prices->count() ? $prices->min() : null,
                'price_max' => $prices->count() ? $prices->max() : null,
                'cover_image' => $project->cover_image,
                'cover_image_url' => $this->primaryProjectImageUrl($project),
                'date_label' => optional($project->created_at)?->format('d M Y'),
            ];
        })->values();

        $summary = [
            'all' => Project::count(),
            'draft' => Project::where('status', 'draft')->count(),
            'ongoing' => Project::where('status', 'ongoing')->count(),
            'completed' => Project::where('status', 'completed')->count(),
            'active' => Project::where('status', 'active')->count(),
            'featured' => Project::where('is_featured', 1)->count(),
        ];

        return response()->json([
            'status' => true,
            'data' => [
                'summary' => $summary,
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

    public function show($id)
    {
        $project = Project::query()
            ->with([
                'city:id,name',
                'units',
            ])
            ->findOrFail($id);

        $gallery = $this->getProjectGallery($project->id);
        $coverImageUrl = $this->primaryProjectImageUrl($project);

        if (empty($gallery) && $coverImageUrl) {
            $gallery = [[
                'id' => 'cover-preview',
                'image_path' => $project->cover_image,
                'image_url' => $coverImageUrl,
                'is_primary' => true,
            ]];
        }

        return response()->json([
            'status' => true,
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
                'cover_image_url' => $coverImageUrl,
                'gallery' => $gallery,
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
        $validated = $this->validateProject($request);

        $project = DB::transaction(function () use ($request, $validated) {
            $project = Project::create([
                'title' => $validated['title'],
                'slug' => $this->makeUniqueSlug($validated['title']),
                'city_id' => $validated['city_id'] ?? null,
                'location' => $validated['location'] ?? null,
                'developer' => $validated['developer'] ?? null,
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'] ?? 'draft',
                'cover_image' => null,
                'is_featured' => (int) ($validated['is_featured'] ?? 0),
            ]);

            $units = $this->cleanUnits($request->input('units', []));
            foreach ($units as $unit) {
                $project->units()->create($unit);
            }

            if ($request->hasFile('images')) {
                $files = $request->file('images');
                $files = is_array($files) ? $files : [$files];
                $this->storeProjectImages($project, $files);
            }

            $this->refreshProjectCover($project);

            return $project;
        });

        return response()->json([
            'status' => true,
            'message' => 'Project created successfully.',
            'data' => [
                'id' => $project->id,
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validated = $this->validateProject($request, $project->id);

        DB::transaction(function () use ($request, $validated, $project) {
            $project->update([
                'title' => $validated['title'],
                'slug' => $this->makeUniqueSlug($validated['title'], $project->id),
                'city_id' => $validated['city_id'] ?? null,
                'location' => $validated['location'] ?? null,
                'developer' => $validated['developer'] ?? null,
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'] ?? 'draft',
                'is_featured' => (int) ($validated['is_featured'] ?? 0),
            ]);

            $deleteImageIds = $request->input('delete_image_ids', []);
            if (!is_array($deleteImageIds)) {
                $deleteImageIds = [$deleteImageIds];
            }

            if (!empty($deleteImageIds)) {
                $this->deleteProjectImages($project->id, $deleteImageIds);
            }

            if ($request->hasFile('images')) {
                $files = $request->file('images');
                $files = is_array($files) ? $files : [$files];
                $this->storeProjectImages($project, $files);
            }

            $project->units()->delete();

            $units = $this->cleanUnits($request->input('units', []));
            foreach ($units as $unit) {
                $project->units()->create($unit);
            }

            $this->refreshProjectCover($project);
        });

        return response()->json([
            'status' => true,
            'message' => 'Project updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);

        DB::transaction(function () use ($project) {
            $this->deleteAllProjectImages($project->id);

            if (!empty($project->cover_image)) {
                Storage::disk('public')->delete($project->cover_image);
            }

            $project->units()->delete();
            $project->delete();
        });

        return response()->json([
            'status' => true,
            'message' => 'Project deleted successfully.',
        ]);
    }

    public function toggleFeatured($id)
    {
        $project = Project::findOrFail($id);

        $project->is_featured = !$project->is_featured;
        $project->save();

        return response()->json([
            'status' => true,
            'message' => 'Featured state updated successfully.',
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:draft,ongoing,completed,active',
        ]);

        $project = Project::findOrFail($id);
        $project->status = $request->status;
        $project->save();

        return response()->json([
            'status' => true,
            'message' => 'Status updated successfully.',
        ]);
    }

    private function validateProject(Request $request, $ignoreId = null)
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'city_id' => 'nullable|exists:cities,id',
            'location' => 'nullable|string|max:255',
            'developer' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,ongoing,completed,active',
            'is_featured' => 'nullable|boolean',

            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp,avif|max:8192',

            'delete_image_ids' => 'nullable|array',
            'delete_image_ids.*' => 'integer',

            'units' => 'nullable|array',
            'units.*.title' => 'nullable|string|max:255',
            'units.*.type' => 'nullable|string|max:255',
            'units.*.bedrooms' => 'nullable|integer|min:0',
            'units.*.bathrooms' => 'nullable|integer|min:0',
            'units.*.area' => 'nullable|numeric|min:0',
            'units.*.price' => 'nullable|numeric|min:0',
            'units.*.status' => 'nullable|string|max:50',
        ]);
    }

    private function cleanUnits(array $units): array
    {
        return collect($units)
            ->map(function ($unit) {
                return [
                    'title' => $unit['title'] ?? null,
                    'type' => $unit['type'] ?? null,
                    'bedrooms' => ($unit['bedrooms'] ?? '') !== '' ? $unit['bedrooms'] : null,
                    'bathrooms' => ($unit['bathrooms'] ?? '') !== '' ? $unit['bathrooms'] : null,
                    'area' => ($unit['area'] ?? '') !== '' ? $unit['area'] : null,
                    'price' => ($unit['price'] ?? '') !== '' ? $unit['price'] : null,
                    'status' => $unit['status'] ?? 'available',
                ];
            })
            ->filter(function ($unit) {
                return !empty($unit['title']) ||
                    !empty($unit['type']) ||
                    !is_null($unit['area']) ||
                    !is_null($unit['price']);
            })
            ->values()
            ->toArray();
    }

    private function makeUniqueSlug(string $title, $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base ?: 'project';
        $counter = 1;

        while (
            Project::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = ($base ?: 'project') . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function storeProjectImages(Project $project, array $files): void
    {
        $hasProjectImagesTable = Schema::hasTable('project_images');
        $imageColumn = $this->projectImageColumn();

        $nextSort = 1;
        if ($hasProjectImagesTable && Schema::hasColumn('project_images', 'sort_order')) {
            $nextSort = ((int) DB::table('project_images')
                ->where('project_id', $project->id)
                ->max('sort_order')) + 1;
        }

        foreach ($files as $index => $file) {
            if (!$file) {
                continue;
            }

            $path = $file->store('projects', 'public');

            if (empty($project->cover_image) && $index === 0) {
                $project->cover_image = $path;
                $project->save();
            }

            if ($hasProjectImagesTable && $imageColumn) {
                $insert = [
                    'project_id' => $project->id,
                    $imageColumn => $path,
                ];

                if (Schema::hasColumn('project_images', 'sort_order')) {
                    $insert['sort_order'] = $nextSort + $index;
                }

                if (Schema::hasColumn('project_images', 'is_primary')) {
                    $hasPrimary = DB::table('project_images')
                        ->where('project_id', $project->id)
                        ->where('is_primary', 1)
                        ->exists();

                    $insert['is_primary'] = !$hasPrimary && $index === 0 ? 1 : 0;
                }

                if (Schema::hasColumn('project_images', 'created_at')) {
                    $insert['created_at'] = now();
                }

                if (Schema::hasColumn('project_images', 'updated_at')) {
                    $insert['updated_at'] = now();
                }

                DB::table('project_images')->insert($insert);
            }
        }
    }

    private function deleteProjectImages(int $projectId, array $ids): void
    {
        if (!Schema::hasTable('project_images')) {
            return;
        }

        $imageColumn = $this->projectImageColumn();
        if (!$imageColumn) {
            return;
        }

        $ids = collect($ids)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->values()
            ->toArray();

        if (empty($ids)) {
            return;
        }

        $rows = DB::table('project_images')
            ->where('project_id', $projectId)
            ->whereIn('id', $ids)
            ->get();

        foreach ($rows as $row) {
            $path = $row->{$imageColumn} ?? null;
            if ($path) {
                Storage::disk('public')->delete($path);
            }
        }

        DB::table('project_images')
            ->where('project_id', $projectId)
            ->whereIn('id', $ids)
            ->delete();
    }

    private function deleteAllProjectImages(int $projectId): void
    {
        if (!Schema::hasTable('project_images')) {
            return;
        }

        $imageColumn = $this->projectImageColumn();
        if (!$imageColumn) {
            return;
        }

        $rows = DB::table('project_images')
            ->where('project_id', $projectId)
            ->get();

        foreach ($rows as $row) {
            $path = $row->{$imageColumn} ?? null;
            if ($path) {
                Storage::disk('public')->delete($path);
            }
        }

        DB::table('project_images')
            ->where('project_id', $projectId)
            ->delete();
    }

    private function refreshProjectCover(Project $project): void
    {
        $gallery = $this->getProjectGallery($project->id);

        if (!empty($gallery)) {
            $firstPath = $gallery[0]['image_path'] ?? null;

            if ($firstPath) {
                $project->cover_image = $firstPath;
                $project->save();
                return;
            }
        }

        if (!empty($project->cover_image)) {
            $project->save();
            return;
        }

        $project->cover_image = null;
        $project->save();
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
                'image_url' => $this->fileUrl($path),
                'is_primary' => (bool) ($row->is_primary ?? false),
            ];
        })->values()->toArray();
    }

    private function primaryProjectImageUrl(Project $project): ?string
    {
        $gallery = $this->getProjectGallery($project->id);

        if (!empty($gallery) && !empty($gallery[0]['image_url'])) {
            return $gallery[0]['image_url'];
        }

        return $this->fileUrl($project->cover_image);
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

    private function fileUrl(?string $path): ?string
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
}