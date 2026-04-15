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
    // GET /api/admin/articles
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $category = trim((string) $request->query('category', ''));
        $status = trim((string) $request->query('status', ''));
        $perPage = (int) $request->query('per_page', 10);

        if ($perPage < 1) {
            $perPage = 10;
        }

        if ($perPage > 50) {
            $perPage = 50;
        }

        $query = Blog::query();

        if ($search !== '' && $this->hasColumn('title')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($category !== '' && strtolower($category) !== 'all categories' && $this->hasColumn('category')) {
            $query->where('category', $category);
        }

        if ($status !== '' && strtolower($status) !== 'all status' && $this->hasColumn('status')) {
            $normalizedStatus = strtolower($status);

            if ($normalizedStatus === 'published') {
                $query->whereIn('status', ['Published', 'published']);
            } elseif ($normalizedStatus === 'draft') {
                $query->whereIn('status', ['Draft', 'draft']);
            } else {
                $query->where('status', $status);
            }
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

    // GET /api/admin/articles/meta
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

    // GET /api/admin/articles/{id}
    public function show($id)
    {
        $article = Blog::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Article details fetched successfully',
            'data' => [
                'id' => $article->id,
                'title' => $this->value($article, 'title', 'Untitled'),
                'slug' => $this->value($article, 'slug'),
                'category' => $this->value($article, 'category', 'General'),
                'author' => $this->articleAuthor($article),
                'status' => $this->normalizeStatus($this->value($article, 'status', 'Draft')),
                'excerpt' => $this->value($article, 'excerpt'),
                'description' => $this->value($article, 'description') ?: $this->value($article, 'content'),
                'content' => $this->value($article, 'content'),
                'image' => $this->value($article, 'image'),
                'image_url' => $this->resolveImageUrl($this->value($article, 'image')),
                'created_at' => $this->formatDateTime($this->value($article, 'created_at')),
                'updated_at' => $this->formatDateTime($this->value($article, 'updated_at')),
                'date_label' => $this->formatDateLabel($this->value($article, 'created_at')),
            ],
        ]);
    }

    // POST /api/admin/articles
    public function store(Request $request)
    {
        $rules = [];

        if ($this->hasColumn('title')) {
            $rules['title'] = 'required|string|max:255';
        }

        if ($this->hasColumn('slug')) {
            $rules['slug'] = 'nullable|string|max:255|unique:blogs,slug';
        }

        if ($this->hasColumn('category')) {
            $rules['category'] = 'nullable|string|max:255';
        }

        // author/writer input
        $rules['author'] = 'nullable|string|max:255';
        $rules['writer'] = 'nullable|string|max:255';

        if ($this->hasColumn('status')) {
            $rules['status'] = ['nullable', 'string', Rule::in(['Published', 'Draft', 'published', 'draft'])];
        }

        if ($this->hasColumn('excerpt')) {
            $rules['excerpt'] = 'nullable|string';
        }

        $rules['description'] = 'nullable|string';
        $rules['content'] = 'nullable|string';

        // image support
        $rules['image'] = 'nullable';
        $rules['image_file'] = 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096';
        $rules['image_url'] = 'nullable|string|max:2048';

        $validated = $request->validate($rules);

        $payload = [];

        if ($this->hasColumn('title')) {
            $payload['title'] = $validated['title'] ?? null;
        }

        if ($this->hasColumn('slug')) {
            $payload['slug'] = !empty($validated['slug'] ?? null)
                ? Str::slug($validated['slug'])
                : Str::slug($validated['title'] ?? ('article-' . now()->timestamp)) . '-' . now()->timestamp;
        }

        if ($this->hasColumn('category')) {
            $payload['category'] = $validated['category'] ?? 'General';
        }

        $authorValue = $validated['author']
            ?? $validated['writer']
            ?? optional($request->user())->name
            ?? 'Hectare Admin';

        if ($this->hasColumn('author')) {
            $payload['author'] = $authorValue;
        }

        if ($this->hasColumn('writer')) {
            $payload['writer'] = $authorValue;
        }

        if ($this->hasColumn('status')) {
            $payload['status'] = $validated['status'] ?? 'Draft';
        }

        if ($this->hasColumn('excerpt')) {
            $payload['excerpt'] = $validated['excerpt'] ?? null;
        }

        $descriptionValue = $validated['description'] ?? null;
        $contentValue = $validated['content'] ?? $descriptionValue;

        if ($this->hasColumn('description')) {
            $payload['description'] = $descriptionValue;
        }

        if ($this->hasColumn('content')) {
            $payload['content'] = $contentValue;
        }

        $incomingImage = $this->resolveIncomingImage($request);

        if ($this->hasColumn('image') && $incomingImage !== null) {
            $payload['image'] = $incomingImage;
        }

        $article = new Blog();
        $article->forceFill($payload);
        $article->save();

        return response()->json([
            'success' => true,
            'message' => 'Article created successfully',
            'data' => $this->transformArticle($article->fresh()),
        ], 201);
    }

    // PUT /api/admin/articles/{id}
    public function update(Request $request, $id)
    {
        $article = Blog::findOrFail($id);

        $rules = [];

        if ($this->hasColumn('title')) {
            $rules['title'] = 'required|string|max:255';
        }

        if ($this->hasColumn('slug')) {
            $rules['slug'] = [
                'nullable',
                'string',
                'max:255',
                Rule::unique('blogs', 'slug')->ignore($article->id),
            ];
        }

        if ($this->hasColumn('category')) {
            $rules['category'] = 'nullable|string|max:255';
        }

        $rules['author'] = 'nullable|string|max:255';
        $rules['writer'] = 'nullable|string|max:255';

        if ($this->hasColumn('status')) {
            $rules['status'] = ['nullable', 'string', Rule::in(['Published', 'Draft', 'published', 'draft'])];
        }

        if ($this->hasColumn('excerpt')) {
            $rules['excerpt'] = 'nullable|string';
        }

        $rules['description'] = 'nullable|string';
        $rules['content'] = 'nullable|string';

        $rules['image'] = 'nullable';
        $rules['image_file'] = 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096';
        $rules['image_url'] = 'nullable|string|max:2048';
        $rules['remove_image'] = 'nullable|boolean';

        $validated = $request->validate($rules);

        $payload = [];

        if ($this->hasColumn('title')) {
            $payload['title'] = $validated['title'] ?? $article->title;
        }

        if ($this->hasColumn('slug')) {
            $payload['slug'] = !empty($validated['slug'] ?? null)
                ? Str::slug($validated['slug'])
                : ($article->slug ?: (Str::slug($validated['title'] ?? 'article') . '-' . now()->timestamp));
        }

        if ($this->hasColumn('category')) {
            $payload['category'] = $validated['category'] ?? $this->value($article, 'category', 'General');
        }

        $authorValue = $validated['author']
            ?? $validated['writer']
            ?? $this->articleAuthor($article)
            ?? 'Hectare Admin';

        if ($this->hasColumn('author')) {
            $payload['author'] = $authorValue;
        }

        if ($this->hasColumn('writer')) {
            $payload['writer'] = $authorValue;
        }

        if ($this->hasColumn('status')) {
            $payload['status'] = $validated['status'] ?? $this->value($article, 'status', 'Draft');
        }

        if ($this->hasColumn('excerpt')) {
            $payload['excerpt'] = $validated['excerpt'] ?? $this->value($article, 'excerpt');
        }

        $descriptionValue = $validated['description'] ?? $this->value($article, 'description');
        $contentValue = $validated['content'] ?? $descriptionValue ?? $this->value($article, 'content');

        if ($this->hasColumn('description')) {
            $payload['description'] = $descriptionValue;
        }

        if ($this->hasColumn('content')) {
            $payload['content'] = $contentValue;
        }

        if ($this->hasColumn('image')) {
            $removeImage = (bool) ($validated['remove_image'] ?? false);
            $incomingImage = $this->resolveIncomingImage($request);

            if ($removeImage) {
                $this->deleteStoredImageIfNeeded($article->image);
                $payload['image'] = null;
            } elseif ($incomingImage !== null) {
                if ($article->image !== $incomingImage) {
                    $this->deleteStoredImageIfNeeded($article->image);
                }
                $payload['image'] = $incomingImage;
            }
        }

        $article->forceFill($payload)->save();

        return response()->json([
            'success' => true,
            'message' => 'Article updated successfully',
            'data' => $this->transformArticle($article->fresh()),
        ]);
    }

    // DELETE /api/admin/articles/{id}
    public function destroy($id)
    {
        $article = Blog::findOrFail($id);

        if ($this->hasColumn('image') && !empty($article->image)) {
            $this->deleteStoredImageIfNeeded($article->image);
        }

        $article->delete();

        return response()->json([
            'success' => true,
            'message' => 'Article deleted successfully',
        ]);
    }

    // POST /api/admin/articles/{id}/publish
    public function publish($id)
    {
        $article = Blog::findOrFail($id);

        if ($this->hasColumn('status')) {
            $article->forceFill(['status' => 'Published'])->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Article published successfully',
            'data' => $this->transformArticle($article->fresh()),
        ]);
    }

    // POST /api/admin/articles/{id}/draft
    public function draft($id)
    {
        $article = Blog::findOrFail($id);

        if ($this->hasColumn('status')) {
            $article->forceFill(['status' => 'Draft'])->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Article moved to draft successfully',
            'data' => $this->transformArticle($article->fresh()),
        ]);
    }

    private function transformArticle($article): array
    {
        return [
            'id' => $article->id,
            'title' => $this->value($article, 'title', 'Untitled'),
            'slug' => $this->value($article, 'slug'),
            'category' => $this->value($article, 'category', 'General'),
            'author' => $this->articleAuthor($article),
            'status' => $this->normalizeStatus($this->value($article, 'status', 'Draft')),
            'date' => $this->formatDateLabel($this->value($article, 'created_at')),
            'created_at' => $this->formatDateTime($this->value($article, 'created_at')),
            'excerpt' => $this->excerpt($article),
            'description' => $this->value($article, 'description') ?: $this->value($article, 'content'),
            'content' => $this->value($article, 'content'),
            'image' => $this->value($article, 'image'),
            'image_url' => $this->resolveImageUrl($this->value($article, 'image')),
        ];
    }

    private function hasColumn(string $column): bool
    {
        return Schema::hasColumn('blogs', $column);
    }

    private function value($model, string $column, $default = null)
    {
        return $this->hasColumn($column) ? ($model->{$column} ?? $default) : $default;
    }

    private function articleAuthor($article): string
    {
        $author = null;

        if ($this->hasColumn('author')) {
            $author = $article->author ?? null;
        }

        if (!$author && $this->hasColumn('writer')) {
            $author = $article->writer ?? null;
        }

        return $author ?: 'Hectare Admin';
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

    private function normalizeStatus($status): string
    {
        $status = (string) $status;

        if (strtolower($status) === 'published') {
            return 'Published';
        }

        if (strtolower($status) === 'draft') {
            return 'Draft';
        }

        return $status;
    }

    private function excerpt($article): ?string
    {
        if ($this->hasColumn('excerpt') && !empty($article->excerpt)) {
            return $article->excerpt;
        }

        if ($this->hasColumn('description') && !empty($article->description)) {
            return Str::limit(strip_tags((string) $article->description), 90);
        }

        if ($this->hasColumn('content') && !empty($article->content)) {
            return Str::limit(strip_tags((string) $article->content), 90);
        }

        return null;
    }

    private function resolveIncomingImage(Request $request): ?string
    {
        if ($request->hasFile('image_file')) {
            return $request->file('image_file')->store('blogs', 'public');
        }

        if ($request->hasFile('image')) {
            return $request->file('image')->store('blogs', 'public');
        }

        $imageUrl = trim((string) $request->input('image_url', ''));
        if ($imageUrl !== '') {
            return $imageUrl;
        }

        $imageValue = $request->input('image');
        if (is_string($imageValue) && trim($imageValue) !== '') {
            return trim($imageValue);
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

        $base = rtrim(config('app.url'), '/');

        if (Str::startsWith($path, '/storage/')) {
            return $base . $path;
        }

        if (Str::startsWith($path, 'storage/')) {
            return $base . '/' . ltrim($path, '/');
        }

        return $base . '/storage/' . ltrim($path, '/');
    }

    private function deleteStoredImageIfNeeded(?string $path): void
    {
        if (!$path) {
            return;
        }

        if (!$this->isLocalStoragePath($path)) {
            return;
        }

        Storage::disk('public')->delete($this->normalizeStoragePath($path));
    }

    private function isLocalStoragePath(string $path): bool
    {
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return false;
        }

        return true;
    }

    private function normalizeStoragePath(string $path): string
    {
        if (Str::startsWith($path, '/storage/')) {
            return ltrim(Str::replaceFirst('/storage/', '', $path), '/');
        }

        if (Str::startsWith($path, 'storage/')) {
            return ltrim(Str::replaceFirst('storage/', '', $path), '/');
        }

        return ltrim($path, '/');
    }
}