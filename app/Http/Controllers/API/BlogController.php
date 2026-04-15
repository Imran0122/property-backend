<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index()
    {
        $query = Blog::query();

        if (Schema::hasColumn('blogs', 'status')) {
            $query->whereIn('status', ['published', 'Published']);
        }

        $blogs = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => true,
            'data' => $blogs->map(fn ($blog) => $this->formatBlog($blog, false)),
        ]);
    }

    public function show($slug)
    {
        $query = Blog::where('slug', $slug);

        if (Schema::hasColumn('blogs', 'status')) {
            $query->whereIn('status', ['published', 'Published']);
        }

        $blog = $query->first();

        if (!$blog) {
            return response()->json([
                'status' => false,
                'message' => 'Blog not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $this->formatBlog($blog, true),
        ]);
    }

    private function formatBlog(Blog $blog, bool $withContent = false): array
    {
        $rawImage = $blog->image ?? null;
        $imageUrl = $this->resolveImageUrl($rawImage);

        $description =
            $blog->description
            ?: $blog->excerpt
            ?: Str::limit(strip_tags((string) $blog->content), 160);

        $categoryName = !empty($blog->category) ? $blog->category : 'Immobilier';
        $authorName =
            !empty($blog->writer)
                ? $blog->writer
                : (!empty($blog->author) ? $blog->author : 'Hectare Admin');

        $readTime =
            !empty($blog->reading_time)
                ? $blog->reading_time
                : '2 MIN';

        $payload = [
            'id' => $blog->id,
            'title' => $blog->title,
            'slug' => $blog->slug,

            'image' => $rawImage,
            'image_url' => $imageUrl,

            'description' => $description,
            'short_description' => Str::limit(strip_tags((string) $description), 150),

            'writer' => $authorName,
            'author' => $authorName,

            'reading_time' => $readTime,
            'read_time' => $readTime,

            'category' => [
                'name' => $categoryName,
            ],

            'date' => optional($blog->created_at)->format('d F Y'),
            'created_at' => optional($blog->created_at)?->toISOString(),
            'updated_at' => optional($blog->updated_at)?->toISOString(),
        ];

        if ($withContent) {
            $payload['content'] = $blog->content;
        }

        return $payload;
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
}