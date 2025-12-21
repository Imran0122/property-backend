<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\User;
use App\Models\Invoice;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => [
                'total_properties' => Property::count(),
                'active_properties' => Property::where('status', 'active')->count(),
                'pending_properties' => Property::where('status', 'pending')->count(),
                'featured_properties' => Property::where('is_featured', 1)->count(),

                'agents' => User::where('is_agent', 1)->count(),
                'active_agents' => User::where('is_agent', 1)->where('status', 'active')->count(),

                'total_revenue' => Invoice::where('status', 'paid')->sum('amount')
            ]
        ]);
    }
}
