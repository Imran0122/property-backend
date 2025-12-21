<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Property;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    /**
     * GET /api/agents/{id}
     * Agent profile + summary (Zameen style)
     */
    public function show($id)
    {
        $agent = User::where('id', $id)
            ->where('is_agent', 1)
            ->first();

        if (!$agent) {
            return response()->json([
                'status' => false,
                'message' => 'Agent not found'
            ], 404);
        }

        $totalProperties = Property::where('user_id', $agent->id)->count();
        $saleProperties = Property::where('user_id', $agent->id)
            ->where('purpose', 'sale')
            ->count();
        $rentProperties = Property::where('user_id', $agent->id)
            ->where('purpose', 'rent')
            ->count();

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $agent->id,
                'name' => $agent->name,
                'email' => $agent->email,
                'phone' => $agent->phone ?? null,
                'whatsapp' => $agent->whatsapp ?? null,
                'profile_image' => $agent->profile_image ?? null,
                'agency_name' => $agent->agency_name ?? null,
                'bio' => $agent->bio ?? null,

                // Zameen-style stats
                'stats' => [
                    'total_properties' => $totalProperties,
                    'sale_properties' => $saleProperties,
                    'rent_properties' => $rentProperties,
                ]
            ]
        ]);
    }

    /**
     * GET /api/agents/{id}/properties
     * Agent ki listings (sale / rent filter ke sath)
     */
    public function properties(Request $request, $id)
    {
        $query = Property::where('user_id', $id)
            ->where('status', 'active')
            ->with('images')
            ->latest();

        // optional filter: sale | rent
        if ($request->has('purpose')) {
            $query->where('purpose', $request->purpose);
        }

        $properties = $query->paginate(12);

        return response()->json([
            'status' => true,
            'data' => $properties->map(function ($p) {
                return [
                    'id' => $p->id,
                    'title' => $p->title,
                    'price' => $p->price,
                    'purpose' => $p->purpose,
                    'bedrooms' => $p->bedrooms,
                    'bathrooms' => $p->bathrooms,
                    'city_id' => $p->city_id,
                    'is_featured' => $p->is_featured,

                    'main_image' => $p->images->first()?->url
                        ?? (isset($p->images[0])
                            ? asset('storage/' . $p->images[0]->image_path)
                            : null),
                ];
            }),
            'meta' => [
                'total' => $properties->total(),
                'current_page' => $properties->currentPage(),
                'last_page' => $properties->lastPage()
            ]
        ]);
    }

    /**
 * GET /api/agents
 * Zameen.com style agents listing
 */
public function index(Request $request)
{
    $agents = User::where('is_agent', 1)
        ->where('status', 'active')
        ->withCount([
            'properties as total_properties' => function ($q) {
                $q->where('status', 'active');
            }
        ])
        ->latest()
        ->paginate(12);

    return response()->json([
        'status' => true,
        'data' => $agents->map(function ($agent) {
            return [
                'id' => $agent->id,
                'name' => $agent->name,
                'phone' => $agent->phone ?? null,
                'profile_image' => $agent->profile_image ?? null,
                'agency_name' => $agent->agency_name ?? null,
                'total_properties' => $agent->total_properties,
            ];
        }),
        'meta' => [
            'total' => $agents->total(),
            'current_page' => $agents->currentPage(),
            'last_page' => $agents->lastPage(),
        ]
    ]);
}

}
