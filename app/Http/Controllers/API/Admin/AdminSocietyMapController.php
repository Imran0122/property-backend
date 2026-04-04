<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocietyImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminSocietyMapController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $cityId = $request->query('city_id');
        $societyId = $request->query('society_id');
        $type = trim((string) $request->query('type', ''));
        $perPage = (int) $request->query('per_page', 20);

        if ($perPage < 1) $perPage = 20;
        if ($perPage > 100) $perPage = 100;

        $query = SocietyImage::with(['society.city']);

        if ($societyId) {
            $query->where('society_id', $societyId);
        }

        if ($this->hasColumn('type') && $type !== '' && $type !== 'all') {
            $query->where('type', $type);
        }

        if ($cityId) {
            $query->whereHas('society', function ($q) use ($cityId) {
                $q->where('city_id', $cityId);
            });
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                if ($this->hasColumn('title')) {
                    $q->where('title', 'like', "%{$search}%");
                }

                $q->orWhereHas('society', function ($s) use ($search) {
                    $s->where('name', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%");
                });
            });
        }

        if ($this->hasColumn('sort_order')) {
            $query->orderBy('sort_order');
        }

        $maps = $query->orderByDesc('id')->paginate($perPage)->withQueryString();

        $maps->getCollection()->transform(function ($map) {
            return $this->transformMap($map);
        });

        return response()->json([
            'success' => true,
            'message' => 'Society maps fetched successfully',
            'data' => [
                'list' => $maps->items(),
                'pagination' => [
                    'current_page' => $maps->currentPage(),
                    'last_page' => $maps->lastPage(),
                    'per_page' => $maps->perPage(),
                    'total' => $maps->total(),
                    'from' => $maps->firstItem(),
                    'to' => $maps->lastItem(),
                ],
            ],
        ]);
    }

    public function show($id)
    {
        $map = SocietyImage::with(['society.city'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Society map detail fetched successfully',
            'data' => $this->transformMap($map),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'society_id' => ['required', 'integer', 'exists:societies,id'],
            'type' => ['nullable', 'string', Rule::in(['society_map', 'map_view'])],
            'title' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'image', 'max:4096'],
        ]);

        $payload = [
            'society_id' => $validated['society_id'],
        ];

        if ($this->hasColumn('type')) {
            $payload['type'] = $validated['type'] ?? 'society_map';
        }

        if ($this->hasColumn('title')) {
            $payload['title'] = $validated['title'] ?? null;
        }

        if ($this->hasColumn('sort_order')) {
            $payload['sort_order'] = $validated['sort_order'] ?? 0;
        }

        if ($request->hasFile('image_file')) {
            $payload['image'] = $request->file('image_file')->store('society-maps', 'public');
        } elseif (!empty($validated['image'])) {
            $payload['image'] = $validated['image'];
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Map image is required',
            ], 422);
        }

        $map = SocietyImage::create($payload);

        return response()->json([
            'success' => true,
            'message' => 'Society map created successfully',
            'data' => $this->transformMap($map->load('society.city')),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $map = SocietyImage::findOrFail($id);

        $validated = $request->validate([
            'society_id' => ['required', 'integer', 'exists:societies,id'],
            'type' => ['nullable', 'string', Rule::in(['society_map', 'map_view'])],
            'title' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'image', 'max:4096'],
        ]);

        $payload = [
            'society_id' => $validated['society_id'],
        ];

        if ($this->hasColumn('type')) {
            $payload['type'] = $validated['type'] ?? ($map->type ?? 'society_map');
        }

        if ($this->hasColumn('title')) {
            $payload['title'] = $validated['title'] ?? ($map->title ?? null);
        }

        if ($this->hasColumn('sort_order')) {
            $payload['sort_order'] = $validated['sort_order'] ?? ($map->sort_order ?? 0);
        }

        if ($request->hasFile('image_file')) {
            if (!empty($map->image) && !Str::startsWith($map->image, ['http://', 'https://']) && Storage::disk('public')->exists($map->image)) {
                Storage::disk('public')->delete($map->image);
            }

            $payload['image'] = $request->file('image_file')->store('society-maps', 'public');
        } elseif (!empty($validated['image'])) {
            $payload['image'] = $validated['image'];
        }

        $map->update($payload);

        return response()->json([
            'success' => true,
            'message' => 'Society map updated successfully',
            'data' => $this->transformMap($map->load('society.city')),
        ]);
    }

    public function destroy($id)
    {
        $map = SocietyImage::findOrFail($id);

        if (!empty($map->image) && !Str::startsWith($map->image, ['http://', 'https://']) && Storage::disk('public')->exists($map->image)) {
            Storage::disk('public')->delete($map->image);
        }

        $map->delete();

        return response()->json([
            'success' => true,
            'message' => 'Society map deleted successfully',
        ]);
    }

    private function transformMap(SocietyImage $map): array
    {
        return [
            'id' => $map->id,
            'society_id' => $map->society_id,
            'society_name' => optional($map->society)->name,
            'city_id' => optional($map->society)->city_id,
            'city_name' => optional(optional($map->society)->city)->name,
            'type' => $this->hasColumn('type') ? ($map->type ?? 'society_map') : 'society_map',
            'title' => $this->hasColumn('title') ? ($map->title ?? null) : null,
            'sort_order' => $this->hasColumn('sort_order') ? (int) ($map->sort_order ?? 0) : 0,
            'image' => $map->image,
            'image_url' => $this->resolveImageUrl($map->image),
            'created_at' => optional($map->created_at)?->format('Y-m-d H:i:s'),
        ];
    }

    private function hasColumn(string $column): bool
    {
        return Schema::hasColumn('society_images', $column);
    }

    private function resolveImageUrl(?string $path): ?string
    {
        if (!$path) return null;

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, '/storage/')) {
            return url(ltrim($path, '/'));
        }

        if (Str::startsWith($path, 'storage/')) {
            return url($path);
        }

        return url('storage/' . ltrim($path, '/'));
    }
}