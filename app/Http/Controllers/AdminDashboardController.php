<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Property;
use App\Models\Lead;
use App\Models\Report;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'properties' => Property::count(),
            'leads' => Lead::count(),
            'reports' => Report::count(),
            'activeAgents' => User::where('role', 'agent')->count(),
        ];

        $latestUsers = User::latest()->take(5)->get();
        $latestProperties = Property::latest()->take(5)->get();
        $latestLeads = Lead::with('property')->latest()->take(5)->get();
        $latestReports = Report::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'latestUsers', 'latestProperties', 'latestLeads', 'latestReports'));
    }
}
