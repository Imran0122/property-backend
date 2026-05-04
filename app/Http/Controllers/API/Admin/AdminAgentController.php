<?php

// namespace App\Http\Controllers\API\Admin;

// use App\Http\Controllers\Controller;
// use App\Models\User;

// class AdminAgentController extends Controller
// {
//     /**
//      * GET /api/admin/agents/pending
//      */
//     public function pending()
//     {
//         $agents = User::where('is_agent', 1)
//             ->where('status', 'pending')
//             ->latest()
//             ->paginate(20);

//         return response()->json([
//             'status' => true,
//             'data' => $agents
//         ]);
//     }

//     /**
//      * POST /api/admin/agents/{id}/approve
//      */
//     public function approve($id)
//     {
//         $agent = User::findOrFail($id);

//         $agent->update([
//             'status' => 'active'
//         ]);

//         return response()->json([
//             'status' => true,
//             'message' => 'Agent approved successfully'
//         ]);
//     }

//     /**
//      * POST /api/admin/agents/{id}/reject
//      */
//     public function reject($id)
//     {
//         $agent = User::findOrFail($id);

//         $agent->update([
//             'status' => 'rejected',
//             'is_agent' => 0
//         ]);

//         return response()->json([
//             'status' => true,
//             'message' => 'Agent rejected'
//         ]);
//     }
// }





namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use Illuminate\Http\Request;

class AdminAgentController extends Controller
{
    public function index(Request $request)
    {
        $query = Agent::with('agency')->withCount('properties');

        if ($request->search) {
            $q = $request->search;
            $query->where(function ($q2) use ($q) {
                $q2->where('name', 'like', "%{$q}%")
                   ->orWhere('email', 'like', "%{$q}%")
                   ->orWhereHas('agency', fn($a) => $a->where('name', 'like', "%{$q}%"));
            });
        }

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $agents = $query->latest()->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'data'    => $agents->items(),
            'meta'    => [
                'total'        => $agents->total(),
                'current_page' => $agents->currentPage(),
                'last_page'    => $agents->lastPage(),
            ],
        ]);
    }

    public function show($id)
    {
        $agent = Agent::with('agency')->withCount('properties')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $agent]);
    }

    public function approve($id)
    {
        $agent = Agent::findOrFail($id);
        $agent->update(['status' => 'active']);
        return response()->json(['success' => true, 'message' => 'Agent approved successfully.']);
    }

    public function reject($id)
    {
        $agent = Agent::findOrFail($id);
        $agent->update(['status' => 'rejected']);
        return response()->json(['success' => true, 'message' => 'Agent rejected.']);
    }

    public function destroy($id)
    {
        $agent = Agent::findOrFail($id);
        $agent->delete();
        return response()->json(['success' => true, 'message' => 'Agent deleted.']);
    }
}