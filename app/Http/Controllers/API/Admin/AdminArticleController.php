<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminArticleController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));
        $perPage = (int) $request->query('per_page', 20);

        if ($perPage < 1) {
            $perPage = 20;
        }

        if ($perPage > 100) {
            $perPage = 100;
        }

        $query = Blog::query();

        if ($search !== '') {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($status !== '') {
            $query->where('status', strtolower($status));
        }

        $articles = $query->latest()->paginate($perPage)->withQueryString();

        $articles->getCollection()->transform(function (Blog $article) {
            return $this->transformArticle($article);
        });

        return response()->json([
            'success' => true,
            'message' => 'Articles fetched successfully',
            'data' => [
                'filters' => [
                    'search' => $search,
                    'status' => $status,
                    'per_page' => $perPage,
                ],
                'list' => $articles->items(),
                'pagination' => [
                    'current_page' => $articles->currentPage(),
                    'last_page' => $articles->lastPage(),
                    'per_page' => $articles->perPage(),
                    'total' => $articles->total(),
                    'from' => $articles->firstItem(),
                    'to' => $articles->lastItem(),
                ],
            ],
        ]);
    }

    public function meta()
    {
        $statuses = Blog::query()
            ->whereNotNull('status')
            ->where('status', '!=', '')
            ->distinct()
            ->pluck('status')
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Article meta fetched successfully',
            'data' => [
                'categories' => [],
                'statuses' => $statuses,
            ],
        ]);
    }

    public function show($id)
    {
        $article = Blog::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Article details fetched successfully',
            'data' => $this->transformArticle($article, true),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:blogs,slug'],
            'content' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'in:draft,published,Draft,Published'],
            'image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'image_url' => ['nullable', 'string', 'max:2048'],
        ]);

        $imagePath = null;

        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('blogs', 'public');
        } elseif (!empty($validated['image_url'])) {
            $imagePath = $validated['image_url'];
        }

        $title = trim($validated['title']);
        $content = $validated['content'] ?? $validated['description'] ?? '';
        $status = strtolower($validated['status'] ?? 'draft');

        $article = new Blog();
        $article->title = $title;
        $article->slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($title) . '-' . time();
        $article->content = $content;
        $article->status = $status;
        $article->image = $imagePath;
        $article->user_id = optional($request->user())->id;
        $article->save();

        return response()->json([
            'success' => true,
            'message' => 'Article created successfully',
            'data' => $this->transformArticle($article->fresh(), true),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $article = Blog::findOrFail($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:blogs,slug,' . $article->id],
            'content' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'in:draft,published,Draft,Published'],
            'image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'image_url' => ['nullable', 'string', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('remove_image')) {
            $this->deleteImageIfLocal($article->image);
            $article->image = null;
        }

        if ($request->hasFile('image_file')) {
            $this->deleteImageIfLocal($article->image);
            $article->image = $request->file('image_file')->store('blogs', 'public');
        } elseif (array_key_exists('image_url', $validated) && !empty($validated['image_url'])) {
            $this->deleteImageIfLocal($article->image);
            $article->image = $validated['image_url'];
        }

        $article->title = trim($validated['title']);
        $article->slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : ($article->slug ?: Str::slug($article->title) . '-' . time());
        $article->content = $validated['content'] ?? $validated['description'] ?? $article->content;
        $article->status = strtolower($validated['status'] ?? $article->status ?? 'draft');
        $article->save();

        return response()->json([
            'success' => true,
            'message' => 'Article updated successfully',
            'data' => $this->transformArticle($article->fresh(), true),
        ]);
    }

    public function destroy($id)
    {
        $article = Blog::findOrFail($id);

        $this->deleteImageIfLocal($article->image);
        $article->delete();

        return response()->json([
            'success' => true,
            'message' => 'Article deleted successfully',
        ]);
    }

    public function publish($id)
    {
        $article = Blog::findOrFail($id);
        $article->status = 'published';
        $article->save();

        return response()->json([
            'success' => true,
            'message' => 'Article published successfully',
            'data' => $this->transformArticle($article->fresh(), true),
        ]);
    }

    public function draft($id)
    {
        $article = Blog::findOrFail($id);
        $article->status = 'draft';
        $article->save();

        return response()->json([
            'success' => true,
            'message' => 'Article moved to draft successfully',
            'data' => $this->transformArticle($article->fresh(), true),
        ]);
    }

    private function transformArticle(Blog $article, bool $withContent = false): array
    {
        $excerpt = Str::limit(strip_tags((string) $article->content), 160);

        $data = [
            'id' => $article->id,
            'title' => $article->title,
            'slug' => $article->slug,
            'status' => strtolower((string) $article->status),
            'date' => optional($article->created_at)->format('d M Y'),
            'created_at' => optional($article->created_at)?->toISOString(),
            'updated_at' => optional($article->updated_at)?->toISOString(),
            'excerpt' => $excerpt,
            'description' => $excerpt,
            'author' => 'Hectare Admin',
            'image' => $article->image,
            'image_url' => $this->resolveImageUrl($article->image),
        ];

        if ($withContent) {
            $data['content'] = $article->content;
        }

        return $data;
    }

    private function resolveImageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, '/storage/')) {
            return url($path);
        }

        if (Str::startsWith($path, 'storage/')) {
            return url('/' . $path);
        }

        return Storage::disk('public')->url($path);
    }

    private function deleteImageIfLocal(?string $path): void
    {
        if (!$path) {
            return;
        }

        if (Str::startsWith($path, ['http://', 'https://', '/storage/', 'storage/'])) {
            if (Str::startsWith($path, 'storage/')) {
                Storage::disk('public')->delete(Str::after($path, 'storage/'));
            }

            return;
        }

        Storage::disk('public')->delete($path);
    }
}