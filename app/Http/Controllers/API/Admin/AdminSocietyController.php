<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Society;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminSocietyController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $cityId = $request->query('city_id');
        $isPopular = $request->query('is_popular');
        $perPage = (int) $request->query('per_page', 20);

        if ($perPage < 1) $perPage = 20;
        if ($perPage > 100) $perPage = 100;

        $query = Society::with(['city:id,name'])->withCount('images');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if (!empty($cityId)) {
            $query->where('city_id', $cityId);
        }

        if ($isPopular !== null && $isPopular !== '' && $isPopular !== 'all') {
            $query->where('is_popular', (int) $isPopular === 1 ? 1 : 0);
        }

        $societies = $query->orderByDesc('id')->paginate($perPage)->withQueryString();

        $societies->getCollection()->transform(function ($society) {
            return $this->transformSociety($society);
        });

        return response()->json([
            'success' => true,
            'message' => 'Societies fetched successfully',
            'data' => [
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

    public function show($id)
    {
        $society = Society::with(['city:id,name', 'images'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Society detail fetched successfully',
            'data' => [
                ...$this->transformSociety($society),
                'description' => $society->description,
                'images' => $society->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'type' => $image->type ?? 'society_map',
                        'title' => $image->title,
                        'sort_order' => (int) ($image->sort_order ?? 0),
                        'image' => $image->image,
                        'image_url' => $this->resolveImageUrl($image->image),
                    ];
                })->values(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('societies', 'slug')],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'image', 'max:4096'],
            'description' => ['nullable', 'string'],
            'views' => ['nullable', 'integer', 'min:0'],
            'is_popular' => ['nullable'],
        ]);

        $payload = [
            'city_id' => $validated['city_id'],
            'name' => $validated['name'],
            'slug' => !empty($validated['slug'])
                ? Str::slug($validated['slug'])
                : Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'views' => $validated['views'] ?? 0,
            'is_popular' => $request->boolean('is_popular'),
        ];

        if ($request->hasFile('image_file')) {
            $payload['image'] = $request->file('image_file')->store('societies', 'public');
        } elseif (!empty($validated['image'])) {
            $payload['image'] = $validated['image'];
        }

        $society = Society::create($payload);

        return response()->json([
            'success' => true,
            'message' => 'Society created successfully',
            'data' => $this->transformSociety($society->load('city')->loadCount('images')),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $society = Society::findOrFail($id);

        $validated = $request->validate([
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('societies', 'slug')->ignore($society->id)],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'image', 'max:4096'],
            'description' => ['nullable', 'string'],
            'views' => ['nullable', 'integer', 'min:0'],
            'is_popular' => ['nullable'],
        ]);

        $payload = [
            'city_id' => $validated['city_id'],
            'name' => $validated['name'],
            'slug' => !empty($validated['slug'])
                ? Str::slug($validated['slug'])
                : Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'views' => $validated['views'] ?? 0,
            'is_popular' => $request->boolean('is_popular'),
        ];

        if ($request->hasFile('image_file')) {
            if (!empty($society->image) && !Str::startsWith($society->image, ['http://', 'https://']) && Storage::disk('public')->exists($society->image)) {
                Storage::disk('public')->delete($society->image);
            }

            $payload['image'] = $request->file('image_file')->store('societies', 'public');
        } elseif (!empty($validated['image'])) {
            $payload['image'] = $validated['image'];
        }

        $society->update($payload);

        return response()->json([
            'success' => true,
            'message' => 'Society updated successfully',
            'data' => $this->transformSociety($society->load('city')->loadCount('images')),
        ]);
    }

    public function destroy($id)
    {
        $society = Society::findOrFail($id);

        if (!empty($society->image) && !Str::startsWith($society->image, ['http://', 'https://']) && Storage::disk('public')->exists($society->image)) {
            Storage::disk('public')->delete($society->image);
        }

        foreach ($society->images as $image) {
            if (!empty($image->image) && !Str::startsWith($image->image, ['http://', 'https://']) && Storage::disk('public')->exists($image->image)) {
                Storage::disk('public')->delete($image->image);
            }
        }

        $society->delete();

        return response()->json([
            'success' => true,
            'message' => 'Society deleted successfully',
        ]);
    }

    private function transformSociety(Society $society): array
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
            'is_popular' => (bool) ($society->is_popular ?? false),
            'maps_count' => (int) ($society->images_count ?? 0),
            'created_at' => optional($society->created_at)?->format('Y-m-d H:i:s'),
        ];
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