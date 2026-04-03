<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Blog listing
     */
    public function index()
    {
        $blogs = Blog::where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $blogs->map(fn ($blog) => $this->formatBlog($blog, false)),
        ]);
    }

    /**
     * Single blog detail
     */
    public function show($slug)
    {
        $blog = Blog::where('slug', $slug)
            ->where('status', 'published')
            ->first();

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

    /**
     * Normalize blog payload for frontend
     */
    private function formatBlog(Blog $blog, bool $withContent = false): array
    {
        $imageUrl = $this->resolveImageUrl($blog->image);

        $description = $blog->description ?: Str::limit(strip_tags((string) $blog->content), 160);
        $categoryName = !empty($blog->category) ? $blog->category : 'Immobilier';
        $authorName = !empty($blog->writer) ? $blog->writer : 'Hectare Admin';
        $readTime = !empty($blog->reading_time) ? $blog->reading_time : '2 MIN';

        return [
            'id' => $blog->id,
            'title' => $blog->title,
            'slug' => $blog->slug,

            // old + new compatible fields
            'image' => $blog->image,
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
        ] + ($withContent ? [
            'content' => $blog->content,
        ] : []);
    }

    /**
     * Resolve blog image URL
     */
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
}