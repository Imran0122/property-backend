<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\Area;
use App\Models\AreaUnit;
use App\Models\City;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyPriceIndex;
use App\Models\PropertyType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PropertyController extends Controller
{
    /**
     * Main listing API
     * GET /api/properties/listing
     */
    public function listing(Request $request): JsonResponse
    {
        $perPage = max((int) $request->get('per_page', 12), 1);

        $query = $this->buildListingQuery($request);

        $tabs = $this->buildTypeTabs(clone $query);
        $locations = $this->buildAreaBuckets(clone $query);

        $paginator = $query->paginate($perPage)->withQueryString();
        $authUserId = $request->user()?->id;

        $results = $paginator->getCollection()
            ->map(fn ($property) => $this->formatListingCard($property, $authUserId))
            ->values();

        $cityName = null;
        if ($request->filled('city_id')) {
            $cityName = City::find($request->city_id)?->name;
        }

        return response()->json([
            'status' => true,
            'data' => [
                'summary' => [
                    'title' => $this->buildListingTitle($request, $cityName),
                    'total' => $paginator->total(),
                    'page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                ],
                'applied_filters' => [
                    'purpose' => $request->get('purpose'),
                    'category' => $request->get('category'),
                    'city_id' => $request->get('city_id'),
                    'area_id' => $request->get('area_id'),
                    'property_type_id' => $request->get('property_type_id'),
                    'keyword' => $request->get('keyword'),
                    'min_price' => $request->get('min_price'),
                    'max_price' => $request->get('max_price'),
                    'min_area' => $request->get('min_area', $request->get('area_min')),
                    'max_area' => $request->get('max_area', $request->get('area_max')),
                    'bedrooms' => $request->get('bedrooms'),
                    'bathrooms' => $request->get('bathrooms'),
                    'sort' => $request->get('sort', 'latest'),
                ],
                'tabs' => $tabs,
                'locations' => $locations,
                'results' => $results,
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ],
            ],
        ]);
    }

    /**
     * Backward compatible endpoints
     */
    public function index(Request $request): JsonResponse
    {
        return $this->listing($request);
    }

    public function searchApi(Request $request): JsonResponse
    {
        return $this->listing($request);
    }

    public function filter(Request $request): JsonResponse
    {
        return $this->listing($request);
    }

    /**
     * Listing filter meta
     * GET /api/properties/listing/meta
     */
    public function listingMeta(Request $request): JsonResponse
    {
        $baseQuery = Property::query();

        if (Schema::hasColumn('properties', 'status')) {
            $baseQuery->whereIn('status', ['active', 'published']);
        }

        if ($request->filled('city_id')) {
            $baseQuery->where('city_id', $request->city_id);
        }

        if ($request->filled('purpose')) {
            $baseQuery->where('purpose', $request->purpose);
        }

        $minPrice = (clone $baseQuery)->min('price');
        $maxPrice = (clone $baseQuery)->max('price');
        $minArea = (clone $baseQuery)->min('area_size');
        $maxArea = (clone $baseQuery)->max('area_size');

        $areas = collect();
        if ($request->filled('city_id')) {
            $areas = Area::withCount([
                'properties' => function ($q) {
                    if (Schema::hasColumn('properties', 'status')) {
                        $q->whereIn('status', ['active', 'published']);
                    }
                }
            ])
                ->where('city_id', $request->city_id)
                ->orderBy('name')
                ->get(['id', 'city_id', 'name', 'slug', 'views']);
        }

        $cities = City::orderBy('name')
            ->get(['id', 'name'])
            ->unique(fn ($city) => Str::lower(trim($city->name)))
            ->values();

        $allTypes = PropertyType::orderBy('name')
            ->get(['id', 'name'])
            ->unique(fn ($type) => Str::lower(trim($type->name)))
            ->values();

        $childTypes = $allTypes->reject(function ($type) {
            return in_array(Str::lower(trim($type->name)), ['homes', 'plots', 'commercial'], true);
        })->values();

        $propertyTypeGroups = [
            'homes' => $childTypes
                ->filter(fn ($type) => $this->resolveTypeGroup($type->name) === 'homes')
                ->values(),
            'plots' => $childTypes
                ->filter(fn ($type) => $this->resolveTypeGroup($type->name) === 'plots')
                ->values(),
            'commercial' => $childTypes
                ->filter(fn ($type) => $this->resolveTypeGroup($type->name) === 'commercial')
                ->values(),
        ];

        return response()->json([
            'status' => true,
            'data' => [
                'objectives' => [
                    ['label' => 'Buy', 'value' => 'sale'],
                    ['label' => 'Rent', 'value' => 'rent'],
                ],
                'categories' => [
                    ['label' => 'Homes', 'value' => 'homes'],
                    ['label' => 'Plots', 'value' => 'plots'],
                    ['label' => 'Commercial', 'value' => 'commercial'],
                ],
                'cities' => $cities,
                'areas' => $areas,
                'property_types' => $allTypes,
                'property_type_groups' => $propertyTypeGroups,
                'amenities' => Amenity::orderBy('name')->get(['id', 'name', 'category']),
                'area_units' => AreaUnit::orderBy('name')->get(['id', 'name', 'slug', 'conversion_to_sqft']),
                'bedroom_options' => [1, 2, 3, 4, 5, 6, 7, 8, 10],
                'bathroom_options' => [1, 2, 3, 4, 5, 6, 7, 8, 10],
                'sort_options' => [
                    ['label' => 'Newest', 'value' => 'latest'],
                    ['label' => 'Price: Low to High', 'value' => 'price_asc'],
                    ['label' => 'Price: High to Low', 'value' => 'price_desc'],
                    ['label' => 'Area: Large to Small', 'value' => 'area_desc'],
                    ['label' => 'Oldest', 'value' => 'oldest'],
                ],
                'more_filter_options' => [
                    'with_photos' => true,
                    'agency_type' => ['agent', 'individual'],
                    'posted_within' => ['today', '3', '7', '14'],
                ],
                'ranges' => [
                    'price' => [
                        'min' => $minPrice ? (float) $minPrice : 0,
                        'max' => $maxPrice ? (float) $maxPrice : 0,
                    ],
                    'area' => [
                        'min' => $minArea ? (float) $minArea : 0,
                        'max' => $maxArea ? (float) $maxArea : 0,
                    ],
                ],
            ],
        ]);
    }

    /**
     * SEO listing route
     * GET /api/homes-for-sale/casablanca
     */
    public function locationSearch(Request $request, string $type, string $city): JsonResponse
    {
        $parsed = $this->parseListingType($type);

        if (!$parsed) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid listing type',
            ], 404);
        }

        $cityModel = City::all()->first(function ($item) use ($city) {
            return Str::slug($item->name) === Str::slug($city);
        });

        if (!$cityModel) {
            return response()->json([
                'status' => false,
                'message' => 'City not found',
            ], 404);
        }

        $request->merge([
            'purpose' => $parsed['purpose'],
            'category' => $parsed['category'],
            'city_id' => $cityModel->id,
        ]);

        return $this->listing($request);
    }

    /**
     * GET /api/properties/{id}
     */
    public function show(Request $request, $id): JsonResponse
    {
        $query = Property::with([
            'city',
            'areaDetail',
            'propertyType',
            'images',
            'amenities',
            'features',
            'user',
            'user.agency',
        ]);

        if (Schema::hasColumn('properties', 'status')) {
            $query->whereIn('status', ['active', 'published']);
        }

        $property = $query->find($id);

        if (!$property) {
            return response()->json([
                'status' => false,
                'message' => 'Property not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $this->formatDetailPayload($property, $request->user()?->id),
        ]);
    }

    /**
     * GET /api/property/{slug}
     */
    public function showBySlug(Request $request, string $slug): JsonResponse
    {
        $query = Property::with([
            'city',
            'areaDetail',
            'propertyType',
            'images',
            'amenities',
            'features',
            'user',
            'user.agency',
        ]);

        if (Schema::hasColumn('properties', 'status')) {
            $query->whereIn('status', ['active', 'published']);
        }

        $property = $query->where('slug', $slug)->first();

        if (!$property) {
            return response()->json([
                'status' => false,
                'message' => 'Property not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $this->formatDetailPayload($property, $request->user()?->id),
        ]);
    }

    /**
     * Existing dashboard methods
     */
    public function dashboardPropertyStats(Request $request)
    {
        $userId = $request->user()->id;
        $baseQuery = Property::where('user_id', $userId);

        return response()->json([
            'status' => true,
            'stats' => [
                'active' => (clone $baseQuery)->where('status', 'active')->count(),
                'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
                'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
                'expired' => (clone $baseQuery)->where('status', 'expired')->count(),
                'deleted' => (clone $baseQuery)->where('status', 'deleted')->count(),
                'downgraded' => (clone $baseQuery)->where('status', 'downgraded')->count(),
                'inactive' => (clone $baseQuery)->where('status', 'inactive')->count(),
            ],
        ]);
    }

    public function dashboardProperties(Request $request)
    {
        $userId = $request->user()->id;

        $query = Property::where('user_id', $userId)
            ->with(['city', 'propertyType', 'images']);

        if ($request->filled('listing_id')) {
            $query->where('id', $request->listing_id);
        }

        if ($request->filled('status')) {
            $query->where('status', strtolower($request->status));
        }

        if ($request->filled('property_type_id')) {
            $query->where('property_type_id', $request->property_type_id);
        }

        if ($request->filled('purpose')) {
            $query->where('purpose', $request->purpose);
        }

        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        if ($request->category === 'hot') {
            $query->where('is_hot', 1);
        }

        if ($request->category === 'super_hot') {
            $query->where('is_super_hot', 1);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('min_area')) {
            $query->where('area_size', '>=', $request->min_area);
        }

        if ($request->filled('max_area')) {
            $query->where('area_size', '<=', $request->max_area);
        }

        $properties = $query->latest()->paginate(10);

        $data = $properties->getCollection()->map(function ($p) {
            $mainImage = optional($p->images->sortByDesc('is_primary')->first())->url;

            return [
                'id' => $p->id,
                'title' => $p->title,
                'price' => $p->price,
                'city' => $p->city?->name,
                'area' => trim(($p->area_size ?? '') . ' ' . ($p->area_unit ?? '')),
                'purpose' => $p->purpose,
                'status' => $p->status,
                'platform' => 'Wallet',
                'posted_on' => optional($p->created_at)->format('Y-m-d'),
                'main_image' => $mainImage,
                'property_type' => $p->propertyType?->name,
                'bedrooms' => $p->bedrooms,
                'bathrooms' => $p->bathrooms,
                'views' => 0,
                'leads' => 0,
                'is_featured' => (bool) $p->is_featured,
                'is_hot' => (bool) $p->is_hot,
                'is_super_hot' => (bool) $p->is_super_hot,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $data,
            'meta' => [
                'total' => $properties->total(),
                'current_page' => $properties->currentPage(),
                'last_page' => $properties->lastPage(),
                'per_page' => $properties->perPage(),
            ],
        ]);
    }

    public function dashboardPostListingMeta(Request $request): JsonResponse
    {
        $user = $request->user();
        $types = PropertyType::orderBy('name')->get(['id', 'name']);

        return response()->json([
            'status' => true,
            'data' => [
                'purposes' => [
                    ['label' => 'Sell', 'value' => 'sale'],
                    ['label' => 'Rent', 'value' => 'rent'],
                ],
                'categories' => [
                    ['label' => 'Home', 'value' => 'homes'],
                    ['label' => 'Plots', 'value' => 'plots'],
                    ['label' => 'Commercial', 'value' => 'commercial'],
                ],
                'property_types' => $types,
                'property_type_groups' => [
                    'homes' => $types->filter(fn ($type) => $this->resolveTypeGroup($type->name) === 'homes')->values(),
                    'plots' => $types->filter(fn ($type) => $this->resolveTypeGroup($type->name) === 'plots')->values(),
                    'commercial' => $types->filter(fn ($type) => $this->resolveTypeGroup($type->name) === 'commercial')->values(),
                ],
                'cities' => City::orderBy('name')->get(['id', 'name']),
                'area_units' => ['sqft', 'sqm', 'marla', 'kanal'],
                'bedroom_options' => ['studio', 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, '10+'],
                'bathroom_options' => [1, 2, 3, 4, 5, 6, '6+'],
                'platforms' => [
                    ['label' => 'Wallet', 'value' => 'wallet'],
                ],
                'contact' => [
                    'email' => $user?->email,
                    'mobile' => $user?->mobile,
                    'landline' => $user?->landline,
                ],
                'feature_tabs' => [
                    ['key' => 'main_features', 'label' => 'Main Features'],
                    ['key' => 'rooms', 'label' => 'Rooms'],
                    ['key' => 'business_and_communication', 'label' => 'Business and Communication'],
                    ['key' => 'community_features', 'label' => 'Community Features'],
                    ['key' => 'healthcare_recreational', 'label' => 'Healthcare Recreational'],
                    ['key' => 'nearby', 'label' => 'Nearby Locations and Other Facilities'],
                    ['key' => 'other_facilities', 'label' => 'Other Facilities'],
                ],
            ],
        ]);
    }

    public function dashboardPostListingShow(Request $request, int $id): JsonResponse
    {
        $property = Property::with([
            'city',
            'areaDetail',
            'propertyType',
            'features',
            'amenities',
            'images',
            'user',
        ])
            ->where('user_id', $request->user()->id)
            ->find($id);

        if (!$property) {
            return response()->json([
                'status' => false,
                'message' => 'Property not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'property' => [
                    'id' => $property->id,
                    'title' => $property->title,
                    'description' => $property->description,
                    'city_id' => $property->city_id,
                    'area_id' => $property->area_id,
                    'area' => $property->area,
                    'price' => $property->price,
                    'property_type_id' => $property->property_type_id,
                    'category' => $this->resolveTypeGroup($property->propertyType?->name ?? ''),
                    'purpose' => $property->purpose,
                    'bedrooms' => $property->bedrooms,
                    'bathrooms' => $property->bathrooms,
                    'area_size' => $property->area_size,
                    'area_unit' => $property->area_unit,
                    'latitude' => $property->latitude,
                    'longitude' => $property->longitude,
                    'status' => $property->status,
                ],
                'features' => $property->features,
                'amenity_ids' => $property->amenities->pluck('id')->values(),
                'images' => $property->images
                    ->sortByDesc('is_primary')
                    ->values()
                    ->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'url' => $image->url,
                            'is_primary' => (bool) $image->is_primary,
                        ];
                    }),
                'contact' => [
                    'email' => $property->user?->email,
                    'mobile' => $property->user?->mobile,
                    'landline' => $property->user?->landline,
                ],
                'platform' => [
                    'label' => 'Wallet',
                    'value' => 'wallet',
                ],
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->postListingRules($request));
        $user = $request->user();

        $property = DB::transaction(function () use ($request, $validated, $user) {
            $property = Property::create([
                'user_id' => $user->id,
                'title' => $validated['title'],
                'slug' => Str::slug($validated['title']) . '-' . time(),
                'description' => $validated['description'],
                'city_id' => $validated['city_id'],
                'area_id' => $validated['area_id'] ?? null,
                'area' => $validated['area'] ?? null,
                'price' => $validated['price'],
                'property_type_id' => $validated['property_type_id'],
                'purpose' => $validated['purpose'],
                'bedrooms' => $this->normalizeCountValue($request->bedrooms),
                'bathrooms' => $this->normalizeCountValue($request->bathrooms),
                'area_size' => $validated['area_size'] ?? null,
                'area_unit' => $validated['area_unit'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'status' => 'pending',
            ]);

            $property->features()->updateOrCreate(
                ['property_id' => $property->id],
                $this->buildFeaturePayload($request)
            );

            if ($request->filled('amenities')) {
                $property->amenities()->sync($request->amenities);
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('properties', 'public');

                    PropertyImage::create([
                        'property_id' => $property->id,
                        'image_path' => $path,
                        'is_primary' => $index === 0 ? 1 : 0,
                    ]);
                }
            }

            $this->updateUserContactFromListingForm($user, $request);

            return $property->load(['features', 'amenities', 'images']);
        });

        return response()->json([
            'status' => true,
            'message' => 'Property added successfully',
            'data' => $property,
        ]);
    }

    public function update(Request $request, $id)
    {
        $property = Property::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with(['images', 'features', 'amenities'])
            ->first();

        if (!$property) {
            return response()->json([
                'status' => false,
                'message' => 'Property not found',
            ], 404);
        }

        $validated = $request->validate($this->postListingRules($request));

        $property = DB::transaction(function () use ($request, $validated, $property) {
            $property->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'city_id' => $validated['city_id'],
                'area_id' => $validated['area_id'] ?? null,
                'area' => $validated['area'] ?? null,
                'price' => $validated['price'],
                'property_type_id' => $validated['property_type_id'],
                'purpose' => $validated['purpose'],
                'bedrooms' => $this->normalizeCountValue($request->bedrooms),
                'bathrooms' => $this->normalizeCountValue($request->bathrooms),
                'area_size' => $validated['area_size'] ?? null,
                'area_unit' => $validated['area_unit'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
            ]);

            $property->features()->updateOrCreate(
                ['property_id' => $property->id],
                $this->buildFeaturePayload($request)
            );

            $property->amenities()->sync($request->input('amenities', []));

            if ($request->filled('delete_image_ids')) {
                $imagesToDelete = $property->images()
                    ->whereIn('id', $request->delete_image_ids)
                    ->get();

                foreach ($imagesToDelete as $image) {
                    Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }
            }

            if ($request->hasFile('images')) {
                $hasPrimary = $property->images()->where('is_primary', 1)->exists();

                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('properties', 'public');

                    PropertyImage::create([
                        'property_id' => $property->id,
                        'image_path' => $path,
                        'is_primary' => (!$hasPrimary && $index === 0) ? 1 : 0,
                    ]);
                }
            }

            if (!$property->images()->where('is_primary', 1)->exists()) {
                $firstImage = $property->images()->first();
                if ($firstImage) {
                    $firstImage->update(['is_primary' => 1]);
                }
            }

            $this->updateUserContactFromListingForm($request->user(), $request);

            return $property->load(['features', 'amenities', 'images']);
        });

        return response()->json([
            'status' => true,
            'message' => 'Property updated successfully',
            'data' => $property,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $property = Property::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$property) {
            return response()->json([
                'status' => false,
                'message' => 'Property not found',
            ], 404);
        }

        $property->delete();

        return response()->json([
            'status' => true,
            'message' => 'Property deleted successfully',
        ]);
    }

    public function getTypes(): JsonResponse
    {
        $types = PropertyType::select('id', 'name')->orderBy('name')->get();

        return response()->json([
            'status' => true,
            'data' => $types,
        ]);
    }

    /**
     * Flat-table based subtype mapping
     */
    public function getSubTypes($type_id): JsonResponse
    {
        $type = PropertyType::find($type_id);

        if (!$type) {
            return response()->json([
                'status' => false,
                'message' => 'Property type not found',
            ], 404);
        }

        $typeName = Str::lower($type->name);

        $map = [
            'homes' => ['house', 'villa', 'apartment', 'penthouse', 'studio', 'farm house', 'flat', 'room'],
            'plots' => ['plot', 'residential plot', 'commercial plot', 'agricultural land', 'industrial land'],
            'commercial' => ['office', 'shop', 'warehouse', 'building', 'floor', 'factory', 'hall', 'plaza'],
        ];

        $group = null;
        if (str_contains($typeName, 'home')) {
            $group = 'homes';
        } elseif (str_contains($typeName, 'plot')) {
            $group = 'plots';
        } elseif (str_contains($typeName, 'commercial')) {
            $group = 'commercial';
        }

        if (!$group) {
            return response()->json([
                'status' => true,
                'data' => [],
            ]);
        }

        $subTypes = PropertyType::query()
            ->where(function ($query) use ($map, $group) {
                foreach ($map[$group] as $name) {
                    $query->orWhereRaw('LOWER(name) = ?', [$name]);
                }
            })
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $subTypes,
        ]);
    }

    public function priceRange(Request $request): JsonResponse
    {
        $query = $this->buildListingQuery($request, applySorting: false);

        return response()->json([
            'status' => true,
            'data' => [
                'min' => (float) ((clone $query)->min('price') ?? 0),
                'max' => (float) ((clone $query)->max('price') ?? 0),
            ],
        ]);
    }

    public function areaRange(Request $request): JsonResponse
    {
        $query = $this->buildListingQuery($request, applySorting: false);

        return response()->json([
            'status' => true,
            'data' => [
                'min' => (float) ((clone $query)->min('area_size') ?? 0),
                'max' => (float) ((clone $query)->max('area_size') ?? 0),
            ],
        ]);
    }

    public function beds(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => [1, 2, 3, 4, 5, 6, 7, 8, 10],
        ]);
    }

    /**
     * ===========================
     * Helpers
     * ===========================
     */
    private function buildListingQuery(Request $request, bool $applySorting = true): Builder
    {
        $query = Property::query()->with([
            'city',
            'areaDetail',
            'propertyType',
            'images',
            'features',
            'amenities',
            'user',
            'user.agency',
        ]);

        if (Schema::hasColumn('properties', 'status')) {
            $query->whereIn('status', ['active', 'published']);
        }

        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        } elseif ($request->filled('area')) {
            $area = trim((string) $request->area);
            $query->where(function ($q) use ($area) {
                $q->where('area', 'like', '%' . $area . '%')
                    ->orWhereHas('areaDetail', function ($q2) use ($area) {
                        $q2->where('name', 'like', '%' . $area . '%');
                    });
            });
        }

        if ($request->filled('property_type_id')) {
            $query->where('property_type_id', $request->property_type_id);
        }

        if ($request->filled('purpose')) {
            $query->where('purpose', $request->purpose);
        }

        if ($request->filled('category')) {
            $this->applyCategoryFilter($query, $request->category);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $minArea = $request->get('min_area', $request->get('area_min'));
        $maxArea = $request->get('max_area', $request->get('area_max'));

        if (!is_null($minArea) && $minArea !== '') {
            $query->where('area_size', '>=', $minArea);
        }

        if (!is_null($maxArea) && $maxArea !== '') {
            $query->where('area_size', '<=', $maxArea);
        }

        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', '>=', (int) $request->bedrooms);
        }

        if ($request->filled('bathrooms')) {
            $query->where('bathrooms', '>=', (int) $request->bathrooms);
        }

        if ($request->boolean('is_featured')) {
            $query->where('is_featured', 1);
        }

        if ($request->boolean('is_hot')) {
            $query->where('is_hot', 1);
        }

        if ($request->boolean('is_super_hot')) {
            $query->where('is_super_hot', 1);
        }

        if ($request->boolean('with_photos')) {
            $query->whereHas('images');
        }

        if ($request->filled('agency_type')) {
            $agencyType = Str::lower(trim((string) $request->agency_type));

            if ($agencyType === 'agent') {
                $query->whereHas('user', function ($q) {
                    $q->where(function ($inner) {
                        $inner->where('is_agent', 1)
                            ->orWhereNotNull('agency_id');
                    });
                });
            }

            if ($agencyType === 'individual') {
                $query->whereHas('user', function ($q) {
                    $q->where(function ($inner) {
                        $inner->whereNull('agency_id')
                            ->where(function ($sub) {
                                $sub->where('is_agent', 0)
                                    ->orWhereNull('is_agent');
                            });
                    });
                });
            }
        }

        if ($request->filled('posted_within')) {
            $postedWithin = (string) $request->posted_within;

            if ($postedWithin === 'today') {
                $query->whereDate('created_at', now()->toDateString());
            } elseif (in_array($postedWithin, ['3', '7', '14'], true)) {
                $query->where('created_at', '>=', now()->subDays((int) $postedWithin));
            }
        }

        if ($request->filled('amenity_ids')) {
            $amenityIds = is_array($request->amenity_ids)
                ? $request->amenity_ids
                : array_filter(explode(',', (string) $request->amenity_ids));

            if (!empty($amenityIds)) {
                $query->whereHas('amenities', function ($q) use ($amenityIds) {
                    $q->whereIn('amenities.id', $amenityIds);
                });
            }
        }

        if ($request->filled('keyword')) {
            $keyword = trim((string) $request->keyword);

            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%')
                    ->orWhere('description', 'like', '%' . $keyword . '%')
                    ->orWhere('area', 'like', '%' . $keyword . '%')
                    ->orWhereHas('city', function ($q2) use ($keyword) {
                        $q2->where('name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhereHas('areaDetail', function ($q2) use ($keyword) {
                        $q2->where('name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhereHas('propertyType', function ($q2) use ($keyword) {
                        $q2->where('name', 'like', '%' . $keyword . '%');
                    });
            });
        }

        if ($applySorting) {
            $sort = $request->get('sort', 'latest');

            switch ($sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;

                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;

                case 'area_desc':
                    $query->orderBy('area_size', 'desc');
                    break;

                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;

                default:
                    if (Schema::hasColumn('properties', 'is_super_hot')) {
                        $query->orderBy('is_super_hot', 'desc');
                    }
                    if (Schema::hasColumn('properties', 'is_hot')) {
                        $query->orderBy('is_hot', 'desc');
                    }
                    if (Schema::hasColumn('properties', 'is_featured')) {
                        $query->orderBy('is_featured', 'desc');
                    }
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        }

        return $query;
    }

    private function applyCategoryFilter(Builder $query, string $category): void
    {
        $category = Str::lower($category);

        $groups = [
            'homes' => ['homes', 'home', 'house', 'villa', 'apartment', 'penthouse', 'studio', 'flat', 'farm house', 'room'],
            'plots' => ['plots', 'plot', 'residential plot', 'commercial plot', 'agricultural land', 'industrial land'],
            'commercial' => ['commercial', 'office', 'shop', 'warehouse', 'building', 'floor', 'factory', 'hall', 'plaza'],
        ];

        if (!isset($groups[$category])) {
            return;
        }

        $query->whereHas('propertyType', function ($q) use ($groups, $category) {
            $q->where(function ($inner) use ($groups, $category) {
                foreach ($groups[$category] as $name) {
                    $inner->orWhereRaw('LOWER(name) = ?', [Str::lower($name)]);
                }
            });
        });
    }

    // private function formatListingCard(Property $property, ?int $authUserId = null): array
    // {
    //     $mainImage = optional($property->images->sortByDesc('is_primary')->first())->url;

    //     $featuredLabel = null;
    //     if ($property->is_super_hot) {
    //         $featuredLabel = 'super-hot';
    //     } elseif ($property->is_hot) {
    //         $featuredLabel = 'hot';
    //     } elseif ($property->is_featured) {
    //         $featuredLabel = 'featured';
    //     }

    //     $locationText = collect([
    //         $property->areaDetail?->name ?: $property->area,
    //         $property->city?->name,
    //     ])->filter()->implode(', ');

    //     $isFavorite = false;
    //     if ($authUserId) {
    //         $isFavorite = $property->favoritedBy()->where('user_id', $authUserId)->exists();
    //     }

    //     return [
    //         'id' => $property->id,
    //         'slug' => $property->slug,
    //         'title' => $property->title,
    //         'price' => (float) $property->price,
    //         'currency' => 'MAD',
    //         'purpose' => $property->purpose,
    //         'location_text' => $locationText,
    //         'city' => $property->city?->name,
    //         'area_name' => $property->areaDetail?->name ?: $property->area,
    //         'property_type' => $property->propertyType?->name,
    //         'bedrooms' => $property->bedrooms,
    //         'bathrooms' => $property->bathrooms,
    //         'area_size' => $property->area_size,
    //         'area_unit' => $property->area_unit,
    //         'description_short' => Str::limit(strip_tags((string) $property->description), 140),
    //         'is_featured' => (bool) $property->is_featured,
    //         'is_hot' => (bool) $property->is_hot,
    //         'is_super_hot' => (bool) $property->is_super_hot,
    //         'featured_label' => $featuredLabel,
    //         'main_image' => $mainImage,
    //         'images_count' => $property->images->count(),
    //         'is_favorite' => $isFavorite,
    //         'added_at' => optional($property->created_at)->diffForHumans(),
    //         'updated_at' => optional($property->updated_at)->diffForHumans(),
    //         'agent' => $property->user ? [
    //             'id' => $property->user->id,
    //             'name' => $property->user->name,
    //             'agency' => $property->user->agency?->name,
    //             'phone' => $property->user->phone,
    //             'mobile' => $property->user->mobile,
    //             'whatsapp' => $property->user->whatsapp,
    //             'landline' => $property->user->landline,
    //         ] : null,
    //     ];
    // }








    private function formatListingCard(Property $property, ?int $authUserId = null): array
{
    $gallery = $property->images
        ->sortByDesc('is_primary')
        ->values()
        ->map(function ($image) {
            return [
                'id' => $image->id,
                'url' => $image->url,
                'image_url' => $image->url,
                'is_primary' => (bool) $image->is_primary,
            ];
        })
        ->values();

    $mainImage = optional($gallery->first())['image_url'] ?? null;

    $featuredLabel = null;

    if ($property->is_super_hot) {
        $featuredLabel = 'super-hot';
    } elseif ($property->is_hot) {
        $featuredLabel = 'hot';
    } elseif ($property->is_featured) {
        $featuredLabel = 'featured';
    }

    $locationText = collect([
        $property->areaDetail?->name ?: $property->area,
        $property->city?->name,
    ])->filter()->implode(', ');

    $isFavorite = false;

    if ($authUserId) {
        $isFavorite = $property->favoritedBy()
            ->where('user_id', $authUserId)
            ->exists();
    }

    return [
        'id' => $property->id,
        'slug' => $property->slug,
        'title' => $property->title,
        'price' => (float) $property->price,
        'currency' => 'MAD',
        'purpose' => $property->purpose,
        'location_text' => $locationText,
        'city' => $property->city?->name,
        'area_name' => $property->areaDetail?->name ?: $property->area,
        'property_type' => $property->propertyType?->name,
        'bedrooms' => $property->bedrooms,
        'bathrooms' => $property->bathrooms,
        'area_size' => $property->area_size,
        'area_unit' => $property->area_unit,
        'description_short' => Str::limit(strip_tags((string) $property->description), 140),

        'is_featured' => (bool) $property->is_featured,
        'is_hot' => (bool) $property->is_hot,
        'is_super_hot' => (bool) $property->is_super_hot,
        'featured_label' => $featuredLabel,

        'main_image' => $mainImage,
        'images_count' => $gallery->count(),

        // Important for listing page card gallery
        'gallery' => $gallery,
        'gallery_images' => $gallery,
        'images' => $gallery,
        'property_images' => $gallery,

        'is_favorite' => $isFavorite,
        'added_at' => optional($property->created_at)->diffForHumans(),
        'updated_at' => optional($property->updated_at)->diffForHumans(),

        'agent' => $property->user ? [
            'id' => $property->user->id,
            'name' => $property->user->name,
            'agency' => $property->user->agency?->name,
            'phone' => $property->user->phone,
            'mobile' => $property->user->mobile,
            'whatsapp' => $property->user->whatsapp,
            'landline' => $property->user->landline,
        ] : null,
    ];
}




    private function formatDetailPayload(Property $property, ?int $authUserId = null): array
    {
        $mainImage = optional($property->images->sortByDesc('is_primary')->first())->url;

        $locationText = collect([
            $property->areaDetail?->name ?: $property->area,
            $property->city?->name,
        ])->filter()->implode(', ');

        $features = $property->features;

        $amenitiesGrouped = $property->amenities
            ->groupBy(fn ($item) => $item->category ?: 'general')
            ->map(fn ($items) => $items->pluck('name')->values())
            ->toArray();

        $featureGroups = [
            'main_features' => array_values(array_filter([
                $features?->built_year ? 'Year of construction: ' . $features->built_year : null,
                $features?->flooring ? 'Flooring: ' . $features->flooring : null,
                $features?->parking_spaces ? 'Parking spaces: ' . $features->parking_spaces : null,
                $features?->electricity_backup ? 'Electricity backup' : null,
                $features?->central_ac ? 'Central AC' : null,
                $features?->central_heating ? 'Central heating' : null,
                $features?->double_glazed_windows ? 'Double glazed windows' : null,
                $features?->furnished ? 'Furnished' : null,
            ])),
            'rooms' => array_values(array_filter([
                $features?->kitchens ? 'Kitchens: ' . $features->kitchens : null,
                $features?->drawing_room ? 'Drawing room' : null,
                $features?->study_room ? 'Study room' : null,
                $features?->store_room ? 'Store room' : null,
                $features?->servant_quarter ? 'Servant quarter' : null,
                $features?->prayer_room ? 'Prayer room' : null,
                $features?->dining_room ? 'Dining room' : null,
            ])),
            'nearby' => array_values(array_filter([
                $features?->nearby_schools ? 'Nearby schools' : null,
                $features?->nearby_hospitals ? 'Nearby hospitals' : null,
                $features?->nearby_restaurants ? 'Nearby restaurants' : null,
                $features?->nearby_shopping_malls ? 'Nearby shopping malls' : null,
                $features?->nearby_public_transport ? 'Nearby public transport' : null,
                $features?->distance_from_airport ? 'Distance from airport: ' . $features->distance_from_airport : null,
            ])),
        ];

        $similar = Property::with(['city', 'areaDetail', 'propertyType', 'images', 'user', 'user.agency'])
            ->where('id', '!=', $property->id)
            ->where('city_id', $property->city_id)
            ->where('property_type_id', $property->property_type_id)
            ->where('purpose', $property->purpose)
            ->when(Schema::hasColumn('properties', 'status'), function ($q) {
                $q->whereIn('status', ['active', 'published']);
            })
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn ($item) => $this->formatListingCard($item, $authUserId))
            ->values();

        $isFavorite = false;
        if ($authUserId) {
            $isFavorite = $property->favoritedBy()->where('user_id', $authUserId)->exists();
        }

        $featuredLabel = null;
        if ($property->is_super_hot) {
            $featuredLabel = 'super-hot';
        } elseif ($property->is_hot) {
            $featuredLabel = 'hot';
        } elseif ($property->is_featured) {
            $featuredLabel = 'featured';
        }

      return [
    'property' => [
        'id' => $property->id,
        'slug' => $property->slug,
        'title' => $property->title,
        'description' => $property->description,
        'price' => (float) $property->price,
        'currency' => 'MAD',
        'purpose' => $property->purpose,
        'status' => $property->status,
        'property_type' => $property->propertyType?->name,
        'city' => $property->city?->name,
        'area_name' => $property->areaDetail?->name ?: $property->area,
        'location_text' => $locationText,
        'bedrooms' => $property->bedrooms,
        'bathrooms' => $property->bathrooms,
        'area_size' => $property->area_size,
        'area_unit' => $property->area_unit,
        'is_featured' => (bool) $property->is_featured,
        'is_hot' => (bool) $property->is_hot,
        'is_super_hot' => (bool) $property->is_super_hot,
        'featured_label' => $featuredLabel,
        'main_image' => $mainImage,
        'images_count' => $property->images->count(),
        'latitude' => $property->latitude,
        'longitude' => $property->longitude,
        'created_at' => $property->created_at,
        'updated_at' => $property->updated_at,
        'is_favorite' => $isFavorite,
    ],
    'gallery' => $property->images
        ->sortByDesc('is_primary')
        ->values()
        ->map(function ($image) {
            return [
                'id' => $image->id,
                'image_url' => $image->url,
                'is_primary' => (bool) $image->is_primary,
            ];
        }),
    'overview' => [
        'price_per_unit' => $property->area_size > 0 ? round($property->price / $property->area_size, 2) : null,
        'purpose' => $property->purpose,
        'type' => $property->propertyType?->name,
        'bedrooms' => $property->bedrooms,
        'bathrooms' => $property->bathrooms,
        'area_size' => $property->area_size,
        'area_unit' => $property->area_unit,
    ],
    'amenities' => $amenitiesGrouped,
    'features' => $features,
    'feature_groups' => $featureGroups,
    'contact' => [
        'agent_name' => $property->user?->name,
        'email' => $property->user?->email,
        'mobile' => $property->user?->mobile,
        'whatsapp' => $property->user?->whatsapp,
        'landline' => $property->user?->landline,
        'agency' => $property->user?->agency?->name,
    ],
    'location' => [
        'city' => $property->city?->name,
        'area' => $property->areaDetail?->name ?: $property->area,
        'lat' => $property->latitude,
        'lng' => $property->longitude,
    ],
    'finance' => [
        'property_price' => (float) $property->price,
        'default_years' => 25,
        'default_deposit_percent' => 30,
    ],
    'price_index' => [
        'enabled' => false,
        'series' => [],
    ],
    'similar_properties' => $similar,
];
    }

    private function buildTypeTabs(Builder $query): array
    {
        $properties = (clone $query)
            ->reorder()
            ->with('propertyType:id,name')
            ->get(['id', 'property_type_id']);

        $labels = [
            'homes' => 'Homes',
            'plots' => 'Plots',
            'commercial' => 'Commercial',
        ];

        $grouped = $properties->groupBy(function ($property) {
            return $this->resolveTypeGroup($property->propertyType?->name ?? '');
        });

        return collect(['homes', 'plots', 'commercial'])
            ->map(function ($key) use ($grouped, $labels) {
                return [
                    'id' => $key,
                    'value' => $key,
                    'label' => $labels[$key],
                    'count' => $grouped->get($key, collect())->count(),
                ];
            })
            ->filter(fn ($tab) => $tab['count'] > 0)
            ->values()
            ->toArray();
    }

    private function buildAreaBuckets(Builder $query): array
    {
        $rows = (clone $query)
            ->reorder()
            ->selectRaw('area_id, COUNT(*) as total')
            ->whereNotNull('area_id')
            ->groupBy('area_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        if ($rows->isEmpty()) {
            return [];
        }

        $areas = Area::whereIn('id', $rows->pluck('area_id'))->get()->keyBy('id');

        return $rows->map(function ($row) use ($areas) {
            return [
                'id' => $row->area_id,
                'name' => $areas[$row->area_id]->name ?? 'Area',
                'slug' => $areas[$row->area_id]->slug ?? null,
                'count' => (int) $row->total,
            ];
        })->values()->toArray();
    }

    private function buildListingTitle(Request $request, ?string $cityName = null): string
    {
        $purpose = $request->get('purpose') === 'rent' ? 'Rent' : 'Sale';
        $category = $request->get('category');

        $categoryLabel = match ($category) {
            'homes' => 'Homes',
            'plots' => 'Plots',
            'commercial' => 'Commercial Properties',
            default => 'Properties',
        };

        if ($cityName) {
            return "{$categoryLabel} for {$purpose} in {$cityName}";
        }

        return "{$categoryLabel} for {$purpose}";
    }

    private function parseListingType(string $type): ?array
    {
        return match ($type) {
            'homes-for-sale' => ['category' => 'homes', 'purpose' => 'sale'],
            'homes-for-rent' => ['category' => 'homes', 'purpose' => 'rent'],
            'plots-for-sale' => ['category' => 'plots', 'purpose' => 'sale'],
            'plots-for-rent' => ['category' => 'plots', 'purpose' => 'rent'],
            'commercial-for-sale' => ['category' => 'commercial', 'purpose' => 'sale'],
            'commercial-for-rent' => ['category' => 'commercial', 'purpose' => 'rent'],
            default => null,
        };
    }

    private function postListingRules(Request $request): array
    {
        $userId = $request->user()?->id;

        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'city_id' => 'required|exists:cities,id',
            'area_id' => 'required|exists:areas,id',
            'area' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'property_type_id' => 'required|exists:property_types,id',
            'purpose' => 'required|in:sale,rent',
            'bedrooms' => 'nullable',
            'bathrooms' => 'nullable',
            'area_size' => 'nullable|numeric|min:0',
            'area_unit' => 'nullable|string|max:50',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',

            'email' => 'nullable|email|max:255|unique:users,email,' . $userId,
            'mobile' => 'nullable|string|max:50',
            'landline' => 'nullable|string|max:50',

            'amenities' => 'nullable|array',
            'amenities.*' => 'integer|exists:amenities,id',

            'features' => 'nullable|array',

            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',

            'delete_image_ids' => 'nullable|array',
            'delete_image_ids.*' => 'integer|exists:property_images,id',
        ];
    }

    private function buildFeaturePayload(Request $request): array
    {
        $features = $request->input('features', []);

        return [
            'bedrooms' => $this->normalizeCountValue($request->input('bedrooms')),
            'bathrooms' => $this->normalizeCountValue($request->input('bathrooms')),
            'area' => $request->input('area_size'),
            'area_unit' => $request->input('area_unit'),

            'furnished' => $this->toBool(data_get($features, 'furnished')),
            'built_year' => data_get($features, 'built_year'),
            'flooring' => data_get($features, 'flooring'),
            'view' => data_get($features, 'view'),
            'other_main_features' => data_get($features, 'other_main_features'),
            'floors' => data_get($features, 'floors'),
            'parking_spaces' => $this->normalizeCountValue(data_get($features, 'parking_spaces')),
            'electricity_backup' => $this->toBool(data_get($features, 'electricity_backup')),
            'central_ac' => $this->toBool(data_get($features, 'central_ac')),
            'central_heating' => $this->toBool(data_get($features, 'central_heating')),
            'double_glazed_windows' => $this->toBool(data_get($features, 'double_glazed_windows')),

            'other_rooms' => data_get($features, 'other_rooms'),
            'kitchens' => $this->normalizeCountValue(data_get($features, 'kitchens')),
            'drawing_room' => $this->toBool(data_get($features, 'drawing_room')),
            'study_room' => $this->toBool(data_get($features, 'study_room')),
            'store_room' => $this->toBool(data_get($features, 'store_room')),
            'servant_quarter' => $this->toBool(data_get($features, 'servant_quarter')),
            'prayer_room' => $this->toBool(data_get($features, 'prayer_room')),
            'dining_room' => $this->toBool(data_get($features, 'dining_room')),

            'other_business_communication' => data_get($features, 'other_business_communication'),
            'broadband_internet_access' => $this->toBool(data_get($features, 'broadband_internet_access')),
            'satellite_or_cable_tv_ready' => $this->toBool(data_get($features, 'satellite_or_cable_tv_ready')),
            'intercom' => $this->toBool(data_get($features, 'intercom')),

            'other_community_facilities' => data_get($features, 'other_community_facilities'),
            'community_lawn_or_garden' => $this->toBool(data_get($features, 'community_lawn_or_garden')),
            'community_swimming_pool' => $this->toBool(data_get($features, 'community_swimming_pool')),
            'community_gym' => $this->toBool(data_get($features, 'community_gym')),
            'first_aid_or_medical_centre' => $this->toBool(data_get($features, 'first_aid_or_medical_centre')),
            'day_care_centre' => $this->toBool(data_get($features, 'day_care_centre')),
            'kids_play_area' => $this->toBool(data_get($features, 'kids_play_area')),
            'barbecue_area' => $this->toBool(data_get($features, 'barbecue_area')),
            'mosque' => $this->toBool(data_get($features, 'mosque')),
            'community_centre' => $this->toBool(data_get($features, 'community_centre')),

            'other_healthcare_recreation' => data_get($features, 'other_healthcare_recreation'),
            'lawn_or_garden' => $this->toBool(data_get($features, 'lawn_or_garden')),
            'swimming_pool' => $this->toBool(data_get($features, 'swimming_pool')),
            'sauna' => $this->toBool(data_get($features, 'sauna')),
            'jacuzzi' => $this->toBool(data_get($features, 'jacuzzi')),

            'nearby_schools' => data_get($features, 'nearby_schools'),
            'nearby_hospitals' => data_get($features, 'nearby_hospitals'),
            'nearby_restaurants' => data_get($features, 'nearby_restaurants'),
            'nearby_shopping_malls' => data_get($features, 'nearby_shopping_malls'),
            'distance_from_airport' => data_get($features, 'distance_from_airport'),
            'nearby_public_transport' => data_get($features, 'nearby_public_transport'),
            'other_nearby_places' => data_get($features, 'other_nearby_places'),

            'other_facilities' => data_get($features, 'other_facilities'),
            'maintenance_staff' => $this->toBool(data_get($features, 'maintenance_staff')),
            'security_staff' => $this->toBool(data_get($features, 'security_staff')),
            'facilities_for_disabled' => $this->toBool(data_get($features, 'facilities_for_disabled')),
        ];
    }

    private function updateUserContactFromListingForm($user, Request $request): void
    {
        if (!$user) {
            return;
        }

        $dirty = false;

        if ($request->filled('email') && $user->email !== $request->email) {
            $user->email = $request->email;
            $dirty = true;
        }

        if ($request->has('mobile')) {
            $user->mobile = $request->mobile;
            $dirty = true;
        }

        if ($request->has('landline')) {
            $user->landline = $request->landline;
            $dirty = true;
        }

        if ($dirty) {
            $user->save();
        }
    }

    private function normalizeCountValue($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $value = strtolower(trim($value));

            if ($value === 'studio') {
                return 0;
            }

            if (str_ends_with($value, '+')) {
                $value = rtrim($value, '+');
            }
        }

        return is_numeric($value) ? (int) $value : null;
    }

    private function toBool($value): bool
    {
        return in_array($value, [true, 1, '1', 'true', 'on', 'yes'], true);
    }

    private function resolveTypeGroup(string $typeName): string
    {
        $typeName = Str::lower(trim($typeName));

        $plots = [
            'plot',
            'plots',
            'residential plot',
            'commercial plot',
            'agricultural land',
            'industrial land',
        ];

        $commercial = [
            'commercial',
            'office',
            'shop',
            'warehouse',
            'building',
            'floor',
            'factory',
            'hall',
            'plaza',
        ];

        if (in_array($typeName, $plots, true) || str_contains($typeName, 'plot')) {
            return 'plots';
        }

        if (in_array($typeName, $commercial, true) || str_contains($typeName, 'commercial')) {
            return 'commercial';
        }

        return 'homes';
    }

    private function buildPriceIndex(Property $property): array
    {
        $datasets = [
    [
        'label' => 'exact',
        'query' => function () use ($property) {
            $query = PropertyPriceIndex::query()
                ->selectRaw('month_key, AVG(avg_price) as avg_price, AVG(avg_price_sqft) as avg_price_sqft, SUM(listing_count) as listing_count')
                ->where('city_id', $property->city_id)
                ->where('property_type_id', $property->property_type_id)
                ->where('purpose', $property->purpose)
                ->whereNotNull('avg_price')
                ->where('avg_price', '>', 0);

            if ($property->area_id) {
                $query->where('area_id', $property->area_id);
            } else {
                $query->whereNull('area_id');
            }

            return $query->groupBy('month_key')->orderBy('month_key');
        },
    ],
    [
        'label' => 'city_type_purpose',
        'query' => function () use ($property) {
            return PropertyPriceIndex::query()
                ->selectRaw('month_key, AVG(avg_price) as avg_price, AVG(avg_price_sqft) as avg_price_sqft, SUM(listing_count) as listing_count')
                ->where('city_id', $property->city_id)
                ->where('property_type_id', $property->property_type_id)
                ->where('purpose', $property->purpose)
                ->whereNotNull('avg_price')
                ->where('avg_price', '>', 0)
                ->groupBy('month_key')
                ->orderBy('month_key');
        },
    ],
];

        $rows = collect();

        foreach ($datasets as $dataset) {
            $candidateRows = $dataset['query']()->get();

            if ($candidateRows->count() >= 2) {
                $rows = $candidateRows;
                break;
            }
        }

        if ($rows->count() < 2) {
            return [
                'enabled' => false,
                'series' => [],
            ];
        }

        $rows = $rows->sortBy('month_key')->values();

        return [
            'enabled' => true,
            'series' => [
                '6months' => $this->makePriceIndexSeries($rows->slice(max($rows->count() - 6, 0))->values()),
                '1year' => $this->makePriceIndexSeries($rows->slice(max($rows->count() - 12, 0))->values()),
                'max' => $this->makePriceIndexSeries($rows->values()),
            ],
        ];
    }

    private function makePriceIndexSeries(Collection $rows): array
    {
        if ($rows->count() === 0) {
            return [
                'labels' => [],
                'values' => [],
                'current_price' => 0,
                'price_change' => '0 (0.00%)',
                'previous_price' => 0,
                'twelve_months_ago' => 0,
                'twenty_four_months_ago' => 0,
                'current_month_label' => null,
                'range_label' => null,
            ];
        }

        $rows = $rows->sortBy('month_key')->values();

        $labels = $rows->map(function ($row) {
            return Carbon::parse($row->month_key)->format('M Y');
        })->values()->all();

        $values = $rows->pluck('avg_price')->map(function ($value) {
            return (float) $value;
        })->values()->all();

        $first = (float) ($rows->first()->avg_price ?? 0);
        $last = (float) ($rows->last()->avg_price ?? 0);

        $change = $last - $first;
        $changePct = $first > 0 ? ($change / $first) * 100 : 0;

        $twelveMonthsAgo = $rows->count() >= 12
            ? (float) optional($rows->slice(-12, 1)->first())->avg_price
            : $first;

        $twentyFourMonthsAgo = $rows->count() >= 24
            ? (float) optional($rows->slice(-24, 1)->first())->avg_price
            : $first;

        return [
            'labels' => $labels,
            'values' => $values,
            'current_price' => round($last),
            'price_change' => number_format($change) . ' (' . number_format($changePct, 2) . '%)',
            'previous_price' => round($first),
            'twelve_months_ago' => round($twelveMonthsAgo),
            'twenty_four_months_ago' => round($twentyFourMonthsAgo),
            'current_month_label' => Carbon::parse($rows->last()->month_key)->format('M Y'),
            'range_label' => Carbon::parse($rows->first()->month_key)->format('M Y') . ' - ' . Carbon::parse($rows->last()->month_key)->format('M Y'),
        ];
    }
}