<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /* ---------------- PROPERTIES ---------------- */

    public function pendingProperties()
    {
        $properties = Property::where('status', 'pending')
            ->with(['city', 'propertyType', 'user'])
            ->latest()
            ->paginate(20);

        return response()->json(['status' => true, 'data' => $properties]);
    }

    public function approveProperty($id)
    {
        $property = Property::findOrFail($id);
        $property->update(['status' => 'active']);

        return response()->json(['status' => true, 'message' => 'Property approved successfully']);
    }

    public function rejectProperty($id)
    {
        $property = Property::findOrFail($id);
        $property->update(['status' => 'rejected']);

        return response()->json(['status' => true, 'message' => 'Property rejected']);
    }

    public function featureProperty(Request $request, $id)
    {
        $property = Property::findOrFail($id);
        $days = $request->get('days', 30);
        $property->update(['is_featured' => 1, 'featured_until' => now()->addDays($days)]);

        return response()->json(['status' => true, 'message' => 'Property featured successfully']);
    }

    public function unfeatureProperty($id)
    {
        $property = Property::findOrFail($id);
        $property->update(['is_featured' => 0, 'featured_until' => null]);

        return response()->json(['status' => true, 'message' => 'Property unfeatured']);
    }

    /* ---------------- AGENTS ---------------- */

    public function pendingAgents()
    {
        $agents = User::where('is_agent', 1)
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);

        return response()->json(['status' => true, 'data' => $agents]);
    }

    public function approveAgent($id)
    {
        $agent = User::findOrFail($id);
        $agent->update(['status' => 'active']);

        return response()->json(['status' => true, 'message' => 'Agent approved successfully']);
    }

    public function rejectAgent($id)
    {
        $agent = User::findOrFail($id);
        $agent->update(['status' => 'rejected']);

        return response()->json(['status' => true, 'message' => 'Agent rejected']);
    }
}