<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyInquiry;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * GET /api/dashboard
     */
    public function index(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'status' => true,
            'data' => [
                'total_properties' => Property::where('user_id', $user->id)->count(),
                'featured_properties' => Property::where('user_id', $user->id)->where('is_featured', 1)->count(),
                'total_inquiries' => PropertyInquiry::whereHas('property', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->count(),
                'latest_properties' => Property::where('user_id', $user->id)
                    ->latest()
                    ->limit(5)
                    ->get(),
            ]
        ]);
    }
}
