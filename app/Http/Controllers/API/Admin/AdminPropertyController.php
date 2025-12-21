<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminPropertyController extends Controller
{
    /**
     * GET /api/admin/properties/pending
     */
    public function pending()
    {
        $properties = Property::where('status', 'pending')
            ->with(['user', 'city'])
            ->latest()
            ->paginate(20);

        return response()->json([
            'status' => true,
            'data' => $properties
        ]);
    }

    /**
     * POST /api/admin/properties/{id}/approve
     */
    public function approve($id)
    {
        $property = Property::findOrFail($id);

        $property->update([
            'status' => 'active'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Property approved successfully'
        ]);
    }

    /**
     * POST /api/admin/properties/{id}/reject
     */
    public function reject(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        $property->update([
            'status' => 'rejected'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Property rejected'
        ]);
    }

    /**
     * POST /api/admin/properties/{id}/feature
     */
    public function feature(Request $request, $id)
    {
        $request->validate([
            'days' => 'required|integer|min:1'
        ]);

        $property = Property::findOrFail($id);

        $property->update([
            'is_featured' => 1,
            'featured_until' => Carbon::now()->addDays($request->days)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Property featured successfully'
        ]);
    }
}
