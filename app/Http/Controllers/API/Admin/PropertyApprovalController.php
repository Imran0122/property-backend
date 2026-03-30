<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PropertyApprovalController extends Controller
{
    // GET /api/admin/properties/pending
    public function pending(Request $request)
    {
        $query = Property::with([
            'user:id,name',
            'city:id,name',
            'propertyType:id,name',
            'features',
            'images',
        ])->where('status', 'pending');

        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        if ($request->filled('property_type_id')) {
            $query->where('property_type_id', $request->property_type_id);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $searchId = preg_replace('/[^0-9]/', '', $search);

            $query->where(function ($q) use ($search, $searchId) {
                $q->where('title', 'like', "%{$search}%");

                if (!empty($searchId)) {
                    $q->orWhere('id', $searchId);
                }

                $q->orWhereHas('user', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                });

                $q->orWhereHas('city', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                });
            });
        }

        $properties = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $properties->getCollection()->transform(function ($property) {
            return [
                'id' => $property->id,
                'approval_code' => 'PD-' . $property->id,
                'property_code' => '#P-' . str_pad($property->id, 4, '0', STR_PAD_LEFT),
                'title' => $property->title,
                'submitted_by' => optional($property->user)->name,
                'city' => optional($property->city)->name,
                'type' => optional($property->propertyType)->name,
                'priority' => $this->calculatePriority($property->created_at),
                'priority_label' => ucfirst($this->calculatePriority($property->created_at)),
                'date' => optional($property->created_at)?->format('d M Y'),
                'created_at' => optional($property->created_at)?->format('Y-m-d H:i:s'),
                'status' => $property->status,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Pending properties fetched successfully',
            'data' => [
                'list' => $properties->items(),
                'pagination' => [
                    'current_page' => $properties->currentPage(),
                    'last_page' => $properties->lastPage(),
                    'per_page' => $properties->perPage(),
                    'total' => $properties->total(),
                    'from' => $properties->firstItem(),
                    'to' => $properties->lastItem(),
                ],
            ],
        ]);
    }

    // GET /api/admin/properties/{id}
    public function show($id)
    {
        $property = Property::with([
            'user:id,name,email,mobile,phone',
            'city:id,name',
            'propertyType:id,name',
            'features',
            'images',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Property details fetched successfully',
            'data' => [
                'id' => $property->id,
                'property_code' => '#P-' . str_pad($property->id, 4, '0', STR_PAD_LEFT),
                'title' => $property->title,
                'slug' => $property->slug,
                'description' => $property->description,
                'owner' => [
                    'name' => optional($property->user)->name,
                    'email' => optional($property->user)->email,
                    'mobile' => optional($property->user)->mobile,
                    'phone' => optional($property->user)->phone,
                ],
                'city' => optional($property->city)->name,
                'type' => optional($property->propertyType)->name,
                'purpose' => $property->purpose,
                'status' => $property->status,
                'price' => $property->price,
                'area' => $property->area,
                'area_size' => $property->area_size,
                'area_unit' => $property->area_unit,
                'bedrooms' => $property->bedrooms,
                'bathrooms' => $property->bathrooms,
                'latitude' => $property->latitude,
                'longitude' => $property->longitude,
                'is_featured' => (bool) $property->is_featured,
                'featured_until' => optional($property->featured_until)?->format('Y-m-d H:i:s'),
                'created_at' => optional($property->created_at)?->format('Y-m-d H:i:s'),
                'updated_at' => optional($property->updated_at)?->format('Y-m-d H:i:s'),
                'features' => $property->features,
                'images' => $property->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'image_path' => $image->image_path,
                        'is_primary' => (bool) $image->is_primary,
                    ];
                })->values(),
            ],
        ]);
    }

    // POST /api/admin/properties/{id}/approve
    public function approve($id)
    {
        $property = Property::findOrFail($id);
        $property->update(['status' => 'active']);

        return response()->json([
            'success' => true,
            'message' => 'Property approved successfully',
            'data' => $this->transformProperty($property->fresh(['user:id,name', 'city:id,name', 'propertyType:id,name'])),
        ]);
    }

    // POST /api/admin/properties/{id}/reject
    public function reject($id)
    {
        $property = Property::findOrFail($id);
        $property->update(['status' => 'rejected']);

        return response()->json([
            'success' => true,
            'message' => 'Property rejected successfully',
            'data' => $this->transformProperty($property->fresh(['user:id,name', 'city:id,name', 'propertyType:id,name'])),
        ]);
    }

    // POST /api/admin/properties/{id}/status
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => [
                'required',
                'string',
                Rule::in(['pending', 'active', 'rejected', 'inactive', 'expired', 'deleted']),
            ],
        ]);

        $property = Property::findOrFail($id);
        $property->update([
            'status' => $validated['status'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Property status updated successfully',
            'data' => $this->transformProperty($property->fresh(['user:id,name', 'city:id,name', 'propertyType:id,name'])),
        ]);
    }

    // POST /api/admin/properties/{id}/feature
    public function feature(Request $request, $id)
    {
        $validated = $request->validate([
            'days' => 'nullable|integer|min:1|max:365',
        ]);

        $days = $validated['days'] ?? 30;

        $property = Property::findOrFail($id);
        $property->update([
            'is_featured' => 1,
            'featured_until' => now()->addDays($days),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Property featured successfully',
            'data' => $this->transformProperty($property->fresh(['user:id,name', 'city:id,name', 'propertyType:id,name'])),
        ]);
    }

    // POST /api/admin/properties/{id}/unfeature
    public function unfeature($id)
    {
        $property = Property::findOrFail($id);

        $property->update([
            'is_featured' => 0,
            'featured_until' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Property unfeatured successfully',
            'data' => $this->transformProperty($property->fresh(['user:id,name', 'city:id,name', 'propertyType:id,name'])),
        ]);
    }

    private function transformProperty($property): array
    {
        return [
            'id' => $property->id,
            'property_code' => '#P-' . str_pad($property->id, 4, '0', STR_PAD_LEFT),
            'title' => $property->title,
            'owner' => optional($property->user)->name,
            'city' => optional($property->city)->name,
            'type' => optional($property->propertyType)->name,
            'price' => $property->price,
            'status' => $property->status,
            'purpose' => $property->purpose,
            'is_featured' => (bool) $property->is_featured,
            'featured_until' => optional($property->featured_until)?->format('Y-m-d H:i:s'),
            'created_at' => optional($property->created_at)?->format('Y-m-d H:i:s'),
        ];
    }

    private function calculatePriority($createdAt): string
    {
        if (!$createdAt) {
            return 'medium';
        }

        $daysPending = Carbon::parse($createdAt)->diffInDays(now());

        return match (true) {
            $daysPending >= 3 => 'high',
            $daysPending >= 1 => 'medium',
            default => 'low',
        };
    }
}