<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SavedSearch;
use Illuminate\Http\Request;

class SavedSearchController extends Controller
{
    public function store(Request $request)
    {
        $search = SavedSearch::create([
            'user_id' => $request->user()->id,
            'filters' => $request->all()
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Search saved successfully',
            'data' => $search
        ]);
    }

    public function index(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => SavedSearch::where('user_id', $request->user()->id)->get()
        ]);
    }
}
