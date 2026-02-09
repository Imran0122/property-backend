<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Blog;

class BlogController extends Controller
{
    // All blogs (listing)
    public function index()
    {
        $blogs = Blog::where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->get([
                'id',
                'title',
                'slug',
                'image',
                'created_at'
            ]);

        return response()->json([
            'status' => true,
            'data' => $blogs
        ]);
    }

    // Single blog by slug (DETAIL PAGE)
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
            'data' => $blog
        ]);
    }
}
