<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Property;
use Illuminate\Http\Request;

class AgencyController extends Controller
{
    /**
     * GET /api/titanium-agencies
     * Return a list (slider) of agencies with basic info + a sample agent/user city
     */
    public function titaniumAgencies(Request $request)
    {
        $agencies = Agency::with(['agents.user'])
            ->whereHas('agents')
            ->latest()
            ->take(12)
            ->get()
            ->map(function($agency) {
                // get first agent user city name (if any)
                $firstAgent = $agency->agents->first();
                $city = null;
                if ($firstAgent && $firstAgent->user && $firstAgent->user->city_id) {
                    $city = optional($firstAgent->user->city)->name ?? null;
                }

                return [
                    'id' => $agency->id,
                    'name' => $agency->name,
                    'logo' => $agency->logo ? asset('storage/' . $agency->logo) : null,
                    'phone' => $agency->phone,
                    'email' => $agency->email,
                    'address' => $agency->address,
                    'description' => $agency->description,
                    'city' => $city,
                ];
            });

        return response()->json($agencies);
    }

    /**
     * GET /api/agencies/{id}
     * Return agency details + all properties listed by agents of this agency (paginated)
     */
    public function show(Request $request, $id)
    {
        $agency = Agency::with('agents.user')->findOrFail($id);

        // collect all user_ids of agents under this agency
        $agentUserIds = $agency->agents->pluck('user_id')->filter()->values()->all();

        // fetch properties belonging to those users
        $propertiesQuery = Property::with(['images', 'city', 'propertyType', 'user'])
            ->whereIn('user_id', $agentUserIds)
            ->where(function($q) {
                // show common statuses — adjust if your app uses different status values
                $q->where('status', 'active')->orWhere('status', 'available')->orWhereNull('status');
            })
            ->latest();

        $perPage = (int) $request->get('per_page', 12);
        $properties = $propertiesQuery->paginate($perPage)->appends($request->query());

        return response()->json([
            'agency' => $agency,
            'properties' => $properties
        ]);
        $agencies = Agency::withCount('agents')->paginate(12);

        return response()->json([
            'status' => true,
            'data' => $agencies
        ]);
    }

      public function index()
    {
        $agencies = Agency::withCount('agents')->paginate(12);

        return response()->json([
            'status' => true,
            'data' => $agencies
        ]);
    }
}
