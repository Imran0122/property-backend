<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminArticleController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $category = trim((string) $request->query('category', ''));
        $status = trim((string) $request->query('status', ''));
        $perPage = (int) $request->query('per_page', 10);

        if ($perPage < 1) $perPage = 10;
        if ($perPage > 50) $perPage = 50;

        $query = Blog::query();

        if ($search !== '' && $this->hasColumn('title')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($category !== '' && strtolower($category) !== 'all categories' && $this->hasColumn('category')) {
            $query->where('category', $category);
        }

        if ($status !== '' && strtolower($category) !== 'all status' && $this->hasColumn('status')) {
            $query->where('status', strtolower($status));
        }

        if ($this->hasColumn('created_at')) {
            $query->orderByDesc('created_at');
        } else {
            $query->orderByDesc('id');
        }

        $articles = $query->paginate($perPage)->withQueryString();

        $articles->getCollection()->transform(function ($article) {
            return $this->transformArticle($article);
        });

        return response()->json([
            'success' => true,
            'message' => 'Articles fetched successfully',
            'data' => [
                'filters' => [
                    'search' => $search,
                    'category' => $category,
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
        $categories = [];
        $statuses = [];

        if ($this->hasColumn('category')) {
            $categories = Blog::query()
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->distinct()
                ->pluck('category')
                ->values();
        }

        if ($this->hasColumn('status')) {
            $statuses = Blog::query()
                ->whereNotNull('status')
                ->where('status', '!=', '')
                ->distinct()
                ->pluck('status')
                ->map(fn ($status) => strtolower((string) $status))
                ->values();
        }

        return response()->json([
            'success' => true,
            'message' => 'Article meta fetched successfully',
            'data' => [
                'categories' => $categories,
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
            'data' => $this->transformArticleDetail($article),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateArticleRequest($request, null);

        $article = new Blog();

        $payload = $this->buildArticlePayload($request, $validated, null);
        $article->forceFill($payload);
        $article->save();

        return response()->json([
            'success' => true,
            'message' => 'Article created successfully',
            'data' => $this->transformArticleDetail($article->fresh()),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $article = Blog::findOrFail($id);

        $validated = $this->validateArticleRequest($request, $article);

        $payload = $this->buildArticlePayload($request, $validated, $article);
        $article->forceFill($payload);
        $article->save();

        return response()->json([
            'success' => true,
            'message' => 'Article updated successfully',
            'data' => $this->transformArticleDetail($article->fresh()),
        ]);
    }

    public function destroy($id)
    {
        $article = Blog::findOrFail($id);

        $oldImage = $this->value($article, 'image');
        $this->deleteStoredImage($oldImage);

        $article->delete();

        return response()->json([
            'success' => true,
            'message' => 'Article deleted successfully',
        ]);
    }

    public function publish($id)
    {
        $article = Blog::findOrFail($id);

        if ($this->hasColumn('status')) {
            $article->forceFill(['status' => 'published'])->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Article published successfully',
            'data' => $this->transformArticleDetail($article->fresh()),
        ]);
    }

    public function draft($id)
    {
        $article = Blog::findOrFail($id);

        if ($this->hasColumn('status')) {
            $article->forceFill(['status' => 'draft'])->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Article moved to draft successfully',
            'data' => $this->transformArticleDetail($article->fresh()),
        ]);
    }

    private function validateArticleRequest(Request $request, ?Blog $article = null): array
    {
        $rules = [];

        if ($this->hasColumn('title')) {
            $rules['title'] = 'required|string|max:255';
        }

        if ($this->hasColumn('slug')) {
            $rules['slug'] = [
                'nullable',
                'string',
                'max:255',
                Rule::unique('blogs', 'slug')->ignore($article?->id),
            ];
        }

        if ($this->hasColumn('category')) {
            $rules['category'] = 'nullable|string|max:255';
        }

        if ($this->hasColumn('author')) {
            $rules['author'] = 'nullable|string|max:255';
        }

        if ($this->hasColumn('writer')) {
            $rules['writer'] = 'nullable|string|max:255';
        }

        if ($this->hasColumn('status')) {
            $rules['status'] = ['nullable', 'string', Rule::in(['published', 'draft', 'Published', 'Draft'])];
        }

        if ($this->hasColumn('excerpt')) {
            $rules['excerpt'] = 'nullable|string';
        }

        if ($this->hasColumn('description')) {
            $rules['description'] = 'nullable|string';
        }

        if ($this->hasColumn('content')) {
            $rules['content'] = 'nullable|string';
        }

        if ($this->hasColumn('reading_time')) {
            $rules['reading_time'] = 'nullable|string|max:50';
        }

        $rules['image_url'] = 'nullable|string|max:2048';
        $rules['image'] = 'nullable|string|max:2048';
        $rules['image_file'] = 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120';

        return $request->validate($rules);
    }

    private function buildArticlePayload(Request $request, array $validated, ?Blog $article = null): array
    {
        $payload = [];

        $title = $validated['title'] ?? $this->value($article, 'title', '');

        if ($this->hasColumn('title')) {
            $payload['title'] = $title;
        }

        if ($this->hasColumn('slug')) {
            $payload['slug'] = !empty($validated['slug'] ?? null)
                ? Str::slug($validated['slug'])
                : ($this->value($article, 'slug') ?: Str::slug($title ?: ('article-' . now()->timestamp)) . '-' . now()->timestamp);
        }

        if ($this->hasColumn('category')) {
            $payload['category'] = $validated['category'] ?? $this->value($article, 'category', 'Immobilier');
        }

        $authorValue = $validated['author']
            ?? $validated['writer']
            ?? $this->value($article, 'author')
            ?? $this->value($article, 'writer')
            ?? optional($request->user())->name
            ?? 'Hectare Admin';

        if ($this->hasColumn('author')) {
            $payload['author'] = $authorValue;
        }

        if ($this->hasColumn('writer')) {
            $payload['writer'] = $authorValue;
        }

        if ($this->hasColumn('status')) {
            $payload['status'] = strtolower((string) ($validated['status'] ?? $this->value($article, 'status', 'draft')));
        }

        if ($this->hasColumn('excerpt')) {
            $payload['excerpt'] = $validated['excerpt'] ?? $this->value($article, 'excerpt');
        }

        if ($this->hasColumn('description')) {
            $payload['description'] = $validated['description']
                ?? $validated['content']
                ?? $this->value($article, 'description');
        }

        if ($this->hasColumn('content')) {
            $payload['content'] = $validated['content']
                ?? $validated['description']
                ?? $this->value($article, 'content');
        }

        if ($this->hasColumn('reading_time')) {
            $payload['reading_time'] = $validated['reading_time']
                ?? $this->value($article, 'reading_time', '2 MIN');
        }

        if ($this->hasColumn('image')) {
            $payload['image'] = $this->prepareImageValue($request, $article);
        }

        return $payload;
    }

    private function prepareImageValue(Request $request, ?Blog $article = null): ?string
    {
        $currentImage = $this->value($article, 'image');

        if ($request->hasFile('image_file')) {
            $this->deleteStoredImage($currentImage);

            $path = $request->file('image_file')->store('blogs', 'public');
            return $path;
        }

        $imageUrl = trim((string) ($request->input('image_url', '') ?: $request->input('image', '')));
        if ($imageUrl !== '') {
            return $imageUrl;
        }

        return $currentImage;
    }

    private function deleteStoredImage(?string $path): void
    {
        if (!$path) return;

        if (Str::startsWith($path, ['http://', 'https://', '/storage/', 'storage/'])) {
            $normalized = ltrim(str_replace('/storage/', '', $path), '/');
            if ($normalized !== $path && Storage::disk('public')->exists($normalized)) {
                Storage::disk('public')->delete($normalized);
            }
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function transformArticle(Blog $article): array
    {
        $image = $this->value($article, 'image');

        return [
            'id' => $article->id,
            'title' => $this->value($article, 'title', 'Untitled'),
            'slug' => $this->value($article, 'slug'),
            'category' => $this->value($article, 'category', 'Immobilier'),
            'author' => $this->getAuthorValue($article),
            'writer' => $this->getAuthorValue($article),
            'status' => strtolower((string) $this->value($article, 'status', 'draft')),
            'date' => $this->formatDateLabel($this->value($article, 'created_at')),
            'created_at' => $this->formatDateTime($this->value($article, 'created_at')),
            'excerpt' => $this->excerpt($article),
            'description' => $this->description($article),
            'image' => $image,
            'image_url' => $this->resolveImageUrl($image),
        ];
    }

    private function transformArticleDetail(Blog $article): array
    {
        $image = $this->value($article, 'image');

        return [
            'id' => $article->id,
            'title' => $this->value($article, 'title', 'Untitled'),
            'slug' => $this->value($article, 'slug'),
            'category' => $this->value($article, 'category', 'Immobilier'),
            'author' => $this->getAuthorValue($article),
            'writer' => $this->getAuthorValue($article),
            'status' => strtolower((string) $this->value($article, 'status', 'draft')),
            'excerpt' => $this->excerpt($article),
            'description' => $this->description($article),
            'content' => $this->value($article, 'content', ''),
            'image' => $image,
            'image_url' => $this->resolveImageUrl($image),
            'reading_time' => $this->value($article, 'reading_time', '2 MIN'),
            'read_time' => $this->value($article, 'reading_time', '2 MIN'),
            'created_at' => $this->formatDateTime($this->value($article, 'created_at')),
            'updated_at' => $this->formatDateTime($this->value($article, 'updated_at')),
            'date' => $this->formatDateLabel($this->value($article, 'created_at')),
        ];
    }

    private function getAuthorValue(Blog $article): string
    {
        return $this->value($article, 'author')
            ?? $this->value($article, 'writer')
            ?? 'Hectare Admin';
    }

    private function description(Blog $article): ?string
    {
        if ($this->hasColumn('description') && !empty($article->description)) {
            return $article->description;
        }

        if ($this->hasColumn('excerpt') && !empty($article->excerpt)) {
            return $article->excerpt;
        }

        if ($this->hasColumn('content') && !empty($article->content)) {
            return Str::limit(strip_tags((string) $article->content), 160);
        }

        return null;
    }

    private function excerpt(Blog $article): ?string
    {
        if ($this->hasColumn('excerpt') && !empty($article->excerpt)) {
            return $article->excerpt;
        }

        if ($this->hasColumn('description') && !empty($article->description)) {
            return Str::limit(strip_tags((string) $article->description), 150);
        }

        if ($this->hasColumn('content') && !empty($article->content)) {
            return Str::limit(strip_tags((string) $article->content), 150);
        }

        return null;
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
            return $path;
        }

        if (Str::startsWith($path, 'storage/')) {
            return '/' . ltrim($path, '/');
        }

        return Storage::disk('public')->url($path);
    }

    private function hasColumn(string $column): bool
    {
        return Schema::hasColumn('blogs', $column);
    }

    private function value($model, string $column, $default = null)
    {
        return $this->hasColumn($column) ? ($model->{$column} ?? $default) : $default;
    }

    private function formatDateLabel($date): ?string
    {
        if (!$date) {
            return null;
        }

        return \Carbon\Carbon::parse($date)->format('d M Y');
    }

    private function formatDateTime($date): ?string
    {
        if (!$date) {
            return null;
        }

        return \Carbon\Carbon::parse($date)->format('Y-m-d H:i:s');
    }
}