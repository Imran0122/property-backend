<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyApprovalController extends Controller
{
    // Approve / Reject Property
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,rejected'
        ]);

        $property = Property::findOrFail($id);
        $property->status = $request->status;
        $property->save();

        return response()->json([
            'status' => true,
            'message' => 'Property status updated successfully'
        ]);
    }

    // Feature Property
    public function feature(Request $request, $id)
    {
        $request->validate([
            'days' => 'required|integer|min:1'
        ]);

        $property = Property::findOrFail($id);

        $property->is_featured = 1;
        $property->featured_until = now()->addDays($request->days);
        $property->save();

        return response()->json([
            'status' => true,
            'message' => 'Property featured successfully'
        ]);
    }
}
