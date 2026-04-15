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

        if ($status !== '' && strtolower($status) !== 'all status' && $this->hasColumn('status')) {
            $query->where('status', strtolower($status));
        }

        if ($this->hasColumn('created_at')) {
            $query->orderByDesc('created_at');
        } else {
            $query->orderByDesc('id');
        }

        $articles = $query->paginate($perPage)->withQueryString();

        $articles->getCollection()->transform(function ($article) {
            return $this->transformArticle($article, false);
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
            'data' => $this->transformArticle($article, true),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->validationRules());

        $article = new Blog();
        $payload = $this->buildPayload($request, $validated, null);

        $article->forceFill($payload);
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

        $validated = $request->validate($this->validationRules($article->id));

        $payload = $this->buildPayload($request, $validated, $article);

        $article->forceFill($payload);
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

        $oldImage = $this->articleImageValue($article);
        $this->deleteStoredImageIfNeeded($oldImage);

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
            'data' => $this->transformArticle($article->fresh(), true),
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
            'data' => $this->transformArticle($article->fresh(), true),
        ]);
    }

    private function validationRules($ignoreId = null): array
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('blogs', 'slug')->ignore($ignoreId),
            ],
            'category' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:255',
            'writer' => 'nullable|string|max:255',
            'status' => ['nullable', 'string', Rule::in(['published', 'draft', 'Published', 'Draft'])],
            'excerpt' => 'nullable|string',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'reading_time' => 'nullable|string|max:50',
            'image' => 'nullable|string|max:2048',
            'image_url' => 'nullable|string|max:2048',
            'image_file' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
        ];
    }

    private function buildPayload(Request $request, array $validated, ?Blog $article = null): array
    {
        $payload = [];

        if ($this->hasColumn('title')) {
            $payload['title'] = $validated['title'] ?? ($article->title ?? null);
        }

        if ($this->hasColumn('slug')) {
            $payload['slug'] = !empty($validated['slug'] ?? null)
                ? Str::slug($validated['slug'])
                : ($article?->slug ?: Str::slug($validated['title'] ?? ('article-' . now()->timestamp)) . '-' . now()->timestamp);
        }

        if ($this->hasColumn('category')) {
            $payload['category'] = $validated['category'] ?? ($article->category ?? 'Immobilier');
        }

        if ($this->hasColumn('author')) {
            $payload['author'] = $validated['author']
                ?? $validated['writer']
                ?? ($article->author ?? optional($request->user())->name ?? 'Hectare Admin');
        }

        if ($this->hasColumn('writer')) {
            $payload['writer'] = $validated['writer']
                ?? $validated['author']
                ?? ($article->writer ?? optional($request->user())->name ?? 'Hectare Admin');
        }

        if ($this->hasColumn('status')) {
            $status = $validated['status'] ?? ($article->status ?? 'draft');
            $payload['status'] = strtolower((string) $status);
        }

        if ($this->hasColumn('excerpt')) {
            $payload['excerpt'] = $validated['excerpt'] ?? ($article->excerpt ?? null);
        }

        if ($this->hasColumn('description')) {
            $payload['description'] = $validated['description']
                ?? $validated['excerpt']
                ?? ($article->description ?? null);
        }

        if ($this->hasColumn('content')) {
            $payload['content'] = $validated['content']
                ?? $validated['description']
                ?? ($article->content ?? null);
        }

        if ($this->hasColumn('reading_time')) {
            $payload['reading_time'] = $validated['reading_time']
                ?? ($article->reading_time ?? '2 MIN');
        }

        $imageColumn = $this->firstExistingColumn(['image', 'featured_image', 'cover_image']);

        if ($imageColumn) {
            $incomingImage = $this->resolveIncomingImage($request, $validated, $article);

            if ($incomingImage !== '__KEEP__') {
                $payload[$imageColumn] = $incomingImage;
            }
        }

        return $payload;
    }

    private function resolveIncomingImage(Request $request, array $validated, ?Blog $article = null)
    {
        if ($request->hasFile('image_file')) {
            $oldImage = $article ? $this->articleImageValue($article) : null;
            $this->deleteStoredImageIfNeeded($oldImage);

            return $request->file('image_file')->store('blogs', 'public');
        }

        $imageInput = trim((string) ($validated['image_url'] ?? $validated['image'] ?? ''));

        if ($imageInput !== '') {
            $oldImage = $article ? $this->articleImageValue($article) : null;

            if ($oldImage && $oldImage !== $imageInput) {
                $this->deleteStoredImageIfNeeded($oldImage);
            }

            return $imageInput;
        }

        return $article ? '__KEEP__' : null;
    }

    private function transformArticle($article, bool $withContent = false): array
    {
        $imagePath = $this->articleImageValue($article);
        $description = $this->value($article, 'description')
            ?? $this->value($article, 'excerpt')
            ?? $this->excerpt($article);

        $content = $this->value($article, 'content') ?? $description;

        return [
            'id' => $article->id,
            'title' => $this->value($article, 'title', 'Untitled'),
            'slug' => $this->value($article, 'slug'),
            'category' => $this->value($article, 'category', 'Immobilier'),
            'author' => $this->value($article, 'author', $this->value($article, 'writer', 'Hectare Admin')),
            'writer' => $this->value($article, 'writer', $this->value($article, 'author', 'Hectare Admin')),
            'status' => $this->normalizeStatus($this->value($article, 'status', 'draft')),
            'date' => $this->formatDateLabel($this->value($article, 'created_at')),
            'created_at' => $this->formatDateTime($this->value($article, 'created_at')),
            'updated_at' => $this->formatDateTime($this->value($article, 'updated_at')),
            'excerpt' => $this->excerpt($article),
            'description' => $description,
            'content' => $withContent ? $content : null,
            'reading_time' => $this->value($article, 'reading_time', '2 MIN'),
            'read_time' => $this->value($article, 'reading_time', '2 MIN'),
            'image' => $imagePath,
            'image_url' => $this->resolveImageUrl($imagePath),
        ];
    }

    private function articleImageValue($article): ?string
    {
        foreach (['image', 'featured_image', 'cover_image'] as $column) {
            if ($this->hasColumn($column) && !empty($article->{$column})) {
                return $article->{$column};
            }
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
            return url($path);
        }

        if (Str::startsWith($path, 'storage/')) {
            return url('/' . $path);
        }

        return Storage::disk('public')->url($path);
    }

    private function deleteStoredImageIfNeeded(?string $path): void
    {
        if (!$path) return;

        if (Str::startsWith($path, ['http://', 'https://', '/storage/', 'storage/'])) {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function firstExistingColumn(array $columns): ?string
    {
        foreach ($columns as $column) {
            if ($this->hasColumn($column)) {
                return $column;
            }
        }

        return null;
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
        if (!$date) return null;
        return \Carbon\Carbon::parse($date)->format('d M Y');
    }

    private function formatDateTime($date): ?string
    {
        if (!$date) return null;
        return \Carbon\Carbon::parse($date)->format('Y-m-d H:i:s');
    }

    private function normalizeStatus($status): string
    {
        $status = strtolower((string) $status);

        if ($status === 'published') return 'Published';
        if ($status === 'draft') return 'Draft';

        return ucfirst($status ?: 'draft');
    }

    private function excerpt($article): ?string
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
}