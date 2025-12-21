<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /* =====================================
       PROPERTIES APPROVAL
    ======================================*/

    // GET /api/admin/properties/pending
    public function pendingProperties()
    {
        $properties = Property::where('status', 'pending')
            ->with(['city', 'propertyType', 'user'])
            ->latest()
            ->paginate(20);

        return response()->json([
            'status' => true,
            'data' => $properties
        ]);
    }

    // POST /api/admin/properties/{id}/approve
    public function approveProperty($id)
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

    // POST /api/admin/properties/{id}/reject
    public function rejectProperty(Request $request, $id)
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

    /* =====================================
       FEATURE PROPERTY (ZAMEEN STYLE)
    ======================================*/

    // POST /api/admin/properties/{id}/feature
    public function featureProperty(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        $property->update([
            'is_featured' => 1,
            'featured_until' => now()->addDays(
                $request->get('days', 7)
            )
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Property featured successfully'
        ]);
    }

    // POST /api/admin/properties/{id}/unfeature
    public function unfeatureProperty($id)
    {
        $property = Property::findOrFail($id);

        $property->update([
            'is_featured' => 0,
            'featured_until' => null
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Property unfeatured'
        ]);
    }

    /* =====================================
       AGENTS APPROVAL
    ======================================*/

    // GET /api/admin/agents/pending
    public function pendingAgents()
    {
        $agents = User::where('is_agent', 1)
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);

        return response()->json([
            'status' => true,
            'data' => $agents
        ]);
    }

    // POST /api/admin/agents/{id}/approve
    public function approveAgent($id)
    {
        $agent = User::findOrFail($id);

        $agent->update([
            'status' => 'active'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Agent approved successfully'
        ]);
    }

    // POST /api/admin/agents/{id}/reject
    public function rejectAgent($id)
    {
        $agent = User::findOrFail($id);

        $agent->update([
            'status' => 'rejected'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Agent rejected'
        ]);
    }
}
