<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Support\Facades\Schema;

class BlogController extends Controller
{
    public function index()
    {
        $query = Blog::query();

        // Check column before filtering
        if (Schema::hasColumn('blogs', 'status')) {
            $query->where('status', 'published');
        }

        $blogs = $query->latest()->limit(6)->get();

        return response()->json($blogs);
    }
}
