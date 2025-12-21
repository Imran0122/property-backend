<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Lead;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class AgentDashboardController extends Controller
{
    public function index()
    {
        $agent = Auth::user();

        $stats = [
            'properties' => Property::where('user_id', $agent->id)->count(),
            'leads'      => Lead::where('agent_id', $agent->id)->count(),
            'messages'   => Message::where('receiver_id', $agent->id)->count(),
            'activeProperties' => Property::where('user_id', $agent->id)->where('status', 'active')->count(),
        ];

        $latestProperties = Property::where('user_id', $agent->id)->latest()->take(5)->get();
        $latestLeads = Lead::where('agent_id', $agent->id)->latest()->take(5)->get();
        $latestMessages = Message::where('receiver_id', $agent->id)->latest()->take(5)->get();

        // Properties per month chart (for agent only)
        $propertiesPerMonth = Property::where('user_id', $agent->id)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        return view('agent.dashboard', compact(
            'stats',
            'latestProperties',
            'latestLeads',
            'latestMessages',
            'propertiesPerMonth'
        ));
    }
}
