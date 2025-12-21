<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Lead;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $agentId = Auth::id();

        // Stats
        $totalProperties = Property::where('user_id', $agentId)->count();
        $activeProperties = Property::where('user_id', $agentId)->where('status', 'active')->count();
        $leadsCount = Lead::whereHas('property', function($q) use ($agentId) {
            $q->where('user_id', $agentId);
        })->count();

        $newLeads = Lead::whereHas('property', function($q) use ($agentId) {
            $q->where('user_id', $agentId);
        })->where('status', 'new')->count();

        // Monthly leads graph
        $monthlyLeads = Lead::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereHas('property', function($q) use ($agentId) {
                $q->where('user_id', $agentId);
            })
            ->groupBy('month')
            ->pluck('total','month');

        return view('agent.dashboard', compact(
            'totalProperties',
            'activeProperties',
            'leadsCount',
            'newLeads',
            'monthlyLeads'
        ));
    }
}
