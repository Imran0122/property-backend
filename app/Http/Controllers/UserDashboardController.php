<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Lead;
use App\Models\SavedSearch;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'favorites' => $user->favorites()->count(),
            'savedSearches' => $user->savedSearches()->count(),
            'leads' => Lead::where('user_id', $user->id)->count(),
            'myProperties' => Property::where('user_id', $user->id)->count(),
        ];

        $favorites = $user->favorites()->with('property')->latest()->take(5)->get();
        $savedSearches = $user->savedSearches()->latest()->take(5)->get();
        $leads = Lead::where('user_id', $user->id)->with('property')->latest()->take(5)->get();
        $myProperties = Property::where('user_id', $user->id)->latest()->take(5)->get();

        return view('user.dashboard', compact(
            'stats',
            'favorites',
            'savedSearches',
            'leads',
            'myProperties'
        ));
    }
}
