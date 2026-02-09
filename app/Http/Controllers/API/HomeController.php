<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function homeBlogs()
    {
        $featured = Blog::orderBy('created_at', 'desc')
            ->take(4)
            ->get(['id', 'title', 'slug', 'image']);

        $latest = Blog::orderBy('created_at', 'desc')
            ->skip(4)
            ->take(2)
            ->get(['id', 'title', 'slug', 'image', 'created_at']);

        return response()->json([
            'status' => true,
            'data' => [
                'featured' => $featured,
                'latest' => $latest->map(function ($blog) {
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
