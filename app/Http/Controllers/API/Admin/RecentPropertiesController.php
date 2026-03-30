<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;

class RecentPropertiesController extends Controller
{
    // GET /api/admin/properties/recent
    public function index(Request $request)
    {
        $requestedStatus = strtolower($request->query('status', 'all'));
        $search = trim((string) $request->query('search', ''));
        $cityId = $request->query('city_id');
        $purpose = trim((string) $request->query('purpose', ''));
        $perPage = (int) $request->query('per_page', 10);

        if ($perPage < 1) {
            $perPage = 10;
        }

        if ($perPage > 50) {
            $perPage = 50;
        }

        $dbStatus = $this->mapFrontendStatusToDb($requestedStatus);

        $query = Property::with([
            'user:id,name',
            'city:id,name',
            'propertyType:id,name',
        ]);

        if ($dbStatus) {
            $query->where('status', $dbStatus);
        }

        if (!empty($cityId)) {
            $query->where('city_id', $cityId);
        }

        if ($purpose !== '') {
            $query->where('purpose', strtolower($purpose));
        }

        if ($search !== '') {
            $searchId = preg_replace('/[^0-9]/', '', $search);

            $query->where(function ($q) use ($search, $searchId) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('purpose', 'like', "%{$search}%");

                if (!empty($searchId)) {
                    $q->orWhere('id', $searchId);
                }

                $q->orWhereHas('user', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                });

                $q->orWhereHas('city', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                });

                $q->orWhereHas('propertyType', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                });
            });
        }

        $properties = $query
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();

        $properties->getCollection()->transform(function ($property) {
            return [
                'id' => $property->id,
                'property_code' => 'PR-' . $property->id,
                'title' => $property->title,
                'owner' => optional($property->user)->name,
                'city' => optional($property->city)->name,
                'type' => optional($property->propertyType)->name,
                'price' => $property->price,
                'status' => $property->status,
                'status_label' => $this->statusLabel($property->status),
                'purpose' => ucfirst((string) $property->purpose),
                'is_featured' => (bool) $property->is_featured,
                'created_at' => optional($property->created_at)?->format('Y-m-d H:i:s'),
                'date_label' => optional($property->created_at)?->format('d M Y'),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Properties fetched successfully',
            'data' => [
                'tabs' => [
                    'all' => Property::count(),
                    'pending' => Property::where('status', 'pending')->count(),
                    'approved' => Property::where('status', 'active')->count(),
                    'rejected' => Property::where('status', 'rejected')->count(),
                ],
                'filters' => [
                    'status' => $requestedStatus,
                    'search' => $search,
                    'city_id' => $cityId,
                    'purpose' => $purpose,
                    'per_page' => $perPage,
                ],
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

    private function mapFrontendStatusToDb(string $status): ?string
    {
        return match ($status) {
            'pending' => 'pending',
            'approved', 'active' => 'active',
            'rejected' => 'rejected',
            'all', '' => null,
            default => null,
        };
    }

    private function statusLabel(?string $status): string
    {
        return match ($status) {
            'active' => 'Approved',
            'pending' => 'Pending',
            'rejected' => 'Rejected',
            default => ucfirst((string) $status),
        };
    }
}