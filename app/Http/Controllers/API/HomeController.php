<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;

class HomeController extends Controller
{
    public function homeBlogs()
    {
        $blogs = Blog::where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return response()->json([
            'status' => true,
            'data' => [
                // First 4 = featured (big cards)
                'featured' => $blogs->take(4)->map(function ($blog) {
                    return [
                        'id' => $blog->id,
                        'title' => $blog->title,
                        'slug' => $blog->slug,
                        'image' => $blog->image,
                        'description' => $blog->description,
                        'date' => $blog->created_at->format('d F Y')
                    ];
                }),

                // Next 2 = small side blogs
                'latest' => $blogs->slice(4, 2)->map(function ($blog) {
                    return [
                        'id' => $blog->id,
                        'title' => $blog->title,
                        'slug' => $blog->slug,
                        'image' => $blog->image,
                        'date' => $blog->created_at->format('d F Y')
                    ];
                })
            ]
        ]);
    }
}
