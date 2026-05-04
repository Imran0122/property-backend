<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class AdminAgentController extends Controller
{
    public function index()
    {
        $agents = User::where('is_agent', 1)->latest()->paginate(20);
        return response()->json(['status' => true, 'data' => $agents]);
    }

    public function pending()
    {
        $agents = User::where('is_agent', 1)
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);
        return response()->json(['status' => true, 'data' => $agents]);
    }

    public function approve($id)
    {
        $agent = User::findOrFail($id);
        $agent->update(['status' => 'active']);
        return response()->json(['status' => true, 'message' => 'Agent approved successfully']);
    }

    public function reject($id)
    {
        $agent = User::findOrFail($id);
        $agent->update(['status' => 'rejected', 'is_agent' => 0]);
        return response()->json(['status' => true, 'message' => 'Agent rejected']);
    }
}