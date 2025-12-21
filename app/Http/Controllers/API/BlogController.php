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

        // Safe check: only apply filter if column exists
        if (Schema::hasColumn('blogs', 'status')) {
            $query->where('status', 'published');
        }

        return response()->json(
            $query->latest()->limit(6)->get()
        );
    }
}
