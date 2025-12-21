<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Message;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Property stats
        $totalProperties = Property::where('user_id', $user->id)->count();
        $activeProperties = Property::where('user_id', $user->id)
                                    ->where('status', 'active')
                                    ->count();
        $inactiveProperties = Property::where('user_id', $user->id)
                                      ->where('status', 'inactive')
                                      ->count();

        // Messages / Leads stats
        $totalLeads = Message::where('receiver_id', $user->id)->count();
        $unreadLeads = Message::where('receiver_id', $user->id)
                              ->where('is_read', false)
                              ->count();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'stats' => [
                'total_properties' => $totalProperties,
                'active_properties' => $activeProperties,
                'inactive_properties' => $inactiveProperties,
                'total_leads' => $totalLeads,
                'unread_leads' => $unreadLeads,
            ]
        ]);
    }



    
    public function myProperties(Request $request)
{
    $user = $request->user();

    // Filters
    $status = $request->query('status'); // active/inactive
    $city = $request->query('city');
    $category = $request->query('category');

    $query = Property::where('user_id', $user->id);

    if ($status) {
        $query->where('status', $status);
    }

    if ($city) {
        $query->where('city_id', $city);
    }

    if ($category) {
        $query->where('category_id', $category);
    }

    // Paginate results
    $properties = $query->with(['city', 'category', 'images'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

    return response()->json($properties);
}

}
