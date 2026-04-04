<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Society;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminSocietyController extends Controller
{
    // GET /api/admin/societies
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $cityId = trim((string) $request->query('city_id', ''));
        $isPopular = strtolower(trim((string) $request->query('is_popular', 'all')));
        $perPage = (int) $request->query('per_page', 20);

        if ($perPage < 1) {
            $perPage = 20;
        }

        if ($perPage > 50) {
            $perPage = 50;
        }

        $query = Society::query()
            ->with(['city:id,name'])
            ->withCount('images');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($cityId !== '') {
            $query->where('city_id', $cityId);
        }

        if ($isPopular !== '' && $isPopular !== 'all') {
            if (in_array($isPopular, ['popular', '1', 'true', 'yes'], true)) {
                $query->where('is_popular', 1);
            }

            if (in_array($isPopular, ['regular', '0', 'false', 'no'], true)) {
                $query->where(function ($q) {
                    $q->where('is_popular', 0)->orWhereNull('is_popular');
                });
            }
        }

        $query->orderByDesc('is_popular')
              ->orderByDesc('views')
              ->orderByDesc('updated_at')
              ->orderByDesc('id');

        $societies = $query->paginate($perPage)->withQueryString();

        $societies->getCollection()->transform(function ($society) {
            return $this->transformSociety($society);
        });

        return response()->json([
            'success' => true,
            'message' => 'Societies fetched successfully',
            'data' => [
                'filters' => [
                    'search' => $search,
                    'city_id' => $cityId,
                    'is_popular' => $isPopular,
                    'per_page' => $perPage,
                ],
                'list' => $societies->items(),
                'pagination' => [
                    'current_page' => $societies->currentPage(),
                    'last_page' => $societies->lastPage(),
                    'per_page' => $societies->perPage(),
                    'total' => $societies->total(),
                    'from' => $societies->firstItem(),
                    'to' => $societies->lastItem(),
                ],
            ],
        ]);
    }

    // GET /api/admin/societies/{id}
    public function show($id)
    {
        $society = Society::with([
                'city:id,name',
                'images:id,society_id,image,created_at,updated_at',
            ])
            ->withCount('images')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Society details fetched successfully',
            'data' => $this->transformSocietyDetail($society),
        ]);
    }

    // POST /api/admin/societies
    public function store(Request $request)
    {
        $validated = $request->validate([
            'city_id' => 'required|integer|exists:cities,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:societies,slug',
            'image' => 'nullable|string|max:255',
            'image_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'description' => 'nullable|string',
            'views' => 'nullable|integer|min:0',
            'is_popular' => 'nullable|boolean',
        ]);

        $imagePath = $validated['image'] ?? null;

        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('societies', 'public');
        }

        $society = Society::create([
            'city_id' => $validated['city_id'],
            'name' => $validated['name'],
            'slug' => $this->makeUniqueSlug(
                $validated['slug'] ?? null,
                $validated['name']
            ),
            'image' => $imagePath,
            'description' => $validated['description'] ?? null,
            'views' => $validated['views'] ?? 0,
            'is_popular' => isset($validated['is_popular']) ? (int) $validated['is_popular'] : 0,
        ]);

        $society->load(['city:id,name', 'images:id,society_id,image,created_at,updated_at'])
                ->loadCount('images');

        return response()->json([
            'success' => true,
            'message' => 'Society created successfully',
            'data' => $this->transformSocietyDetail($society),
        ], 201);
    }

    // PUT /api/admin/societies/{id}
    public function update(Request $request, $id)
    {
        $society = Society::findOrFail($id);

        $validated = $request->validate([
            'city_id' => 'required|integer|exists:cities,id',
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('societies', 'slug')->ignore($society->id),
            ],
            'image' => 'nullable|string|max:255',
            'image_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'description' => 'nullable|string',
            'views' => 'nullable|integer|min:0',
            'is_popular' => 'nullable|boolean',
        ]);

        $imagePath = $society->image;

        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('societies', 'public');
        } elseif (array_key_exists('image', $validated)) {
            $imagePath = $validated['image'] ?? null;
        }

        $society->update([
            'city_id' => $validated['city_id'],
            'name' => $validated['name'],
            'slug' => $this->makeUniqueSlug(
                $validated['slug'] ?? $society->slug,
                $validated['name'],
                $society->id
            ),
            'image' => $imagePath,
            'description' => $validated['description'] ?? null,
            'views' => $validated['views'] ?? 0,
            'is_popular' => isset($validated['is_popular']) ? (int) $validated['is_popular'] : 0,
        ]);

        $society->load(['city:id,name', 'images:id,society_id,image,created_at,updated_at'])
                ->loadCount('images');

        return response()->json([
            'success' => true,
            'message' => 'Society updated successfully',
            'data' => $this->transformSocietyDetail($society),
        ]);
    }

    // DELETE /api/admin/societies/{id}
    public function destroy($id)
    {
        $society = Society::findOrFail($id);
        $society->delete();

        return response()->json([
            'success' => true,
            'message' => 'Society deleted successfully',
        ]);
    }

    private function transformSociety($society): array
    {
        return [
            'id' => $society->id,
            'city_id' => $society->city_id,
            'city_name' => optional($society->city)->name,
            'name' => $society->name,
            'slug' => $society->slug,
            'image' => $society->image,
            'image_url' => $this->resolveImageUrl($society->image),
            'description' => $society->description,
            'views' => (int) ($society->views ?? 0),
            'is_popular' => (int) ($society->is_popular ?? 0),
            'images_count' => (int) ($society->images_count ?? 0),
            'created_at' => optional($society->created_at)?->format('Y-m-d H:i:s'),
            'updated_at' => optional($society->updated_at)?->format('Y-m-d H:i:s'),
        ];
    }

    private function transformSocietyDetail($society): array
    {
        return [
            'id' => $society->id,
            'city_id' => $society->city_id,
            'city_name' => optional($society->city)->name,
            'name' => $society->name,
            'slug' => $society->slug,
            'image' => $society->image,
            'image_url' => $this->resolveImageUrl($society->image),
            'description' => $society->description,
            'views' => (int) ($society->views ?? 0),
            'is_popular' => (int) ($society->is_popular ?? 0),
            'images_count' => (int) ($society->images_count ?? 0),
            'images' => collect($society->images ?? [])->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image' => $image->image,
                    'image_url' => $this->resolveImageUrl($image->image),
                    'created_at' => optional($image->created_at)?->format('Y-m-d H:i:s'),
                    'updated_at' => optional($image->updated_at)?->format('Y-m-d H:i:s'),
                ];
            })->values(),
            'created_at' => optional($society->created_at)?->format('Y-m-d H:i:s'),
            'updated_at' => optional($society->updated_at)?->format('Y-m-d H:i:s'),
        ];
    }

    private function makeUniqueSlug(?string $slug, string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($slug ?: $name);

        if ($base === '') {
            $base = 'society';
        }

        $finalSlug = $base;
        $counter = 2;

        while (
            Society::where('slug', $finalSlug)
                ->when($ignoreId, function ($q) use ($ignoreId) {
                    $q->where('id', '!=', $ignoreId);
                })
                ->exists()
        ) {
            $finalSlug = $base . '-' . $counter;
            $counter++;
        }

        return $finalSlug;
    }

    private function resolveImageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }

        if (Str::startsWith($path, '/storage/')) {
            return url($path);
        }

        if (Str::startsWith($path, 'storage/')) {
            return url('/' . $path);
        }

        if (Str::startsWith($path, '/')) {
            return url($path);
        }

        return url('/storage/' . ltrim($path, '/'));
    }
}