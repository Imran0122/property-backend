<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class AgentApprovalController extends Controller
{
    public function approve($id)
    {
        $agent = User::where('id', $id)
            ->where('is_agent', 1)
            ->firstOrFail();

        $agent->update([
            'status' => 'active'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Agent approved'
        ]);
    }

    public function reject($id)
    {
        $agent = User::where('id', $id)
            ->where('is_agent', 1)
            ->firstOrFail();

        $agent->update([
            'status' => 'rejected'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Agent rejected'
        ]);
    }
}
