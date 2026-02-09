<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Blog;

class BlogController extends Controller
{
    // ===============================
    // BLOG LISTING PAGE
    // ===============================
    public function index()
    {
        $blogs = Blog::where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $blogs->map(function ($blog) {
                return [
                    'id' => $blog->id,
                    'title' => $blog->title,
                    'slug' => $blog->slug,
                    'image' => $blog->image,
                    'description' => $blog->description,
                    'writer' => $blog->writer,
                    'reading_time' => $blog->reading_time,
                    'date' => $blog->created_at->format('d F Y')
                ];
            })
        ]);
    }

    // ===============================
    // BLOG DETAIL PAGE
    // ===============================
    public function show($slug)
    {
        $blog = Blog::where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if (!$blog) {
            return response()->json([
                'status' => false,
                'message' => 'Blog not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $blog->id,
                'title' => $blog->title,
                'slug' => $blog->slug,
                'image' => $blog->image,
                'content' => $blog->content,
                'description' => $blog->description,
                'writer' => $blog->writer,
                'reading_time' => $blog->reading_time,
                'date' => $blog->created_at->format('d F Y')
            ]
        ]);
    }
}
