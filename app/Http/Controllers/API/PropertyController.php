<?php

namespace App\Http\Controllers\API;

use App\Models\PropertyType;
use App\Models\PropertySubtype;
use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PropertyController extends Controller
{
    /**
     * GET /api/properties
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 12);

        $query = Property::query()
            ->with(['city', 'propertyType', 'images', 'amenities', 'features', 'user']);

        /* =========================
           ZAMEEN STYLE CORE FILTERS
        ==========================*/

        // City
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // Area
        if ($request->filled('area_id')) {
            $query->where('area', $request->area_id);
        } elseif ($request->filled('area')) {
            $query->where('area', $request->area);
        }

        // Property Type
        if ($request->filled('property_type_id')) {
            $query->where('property_type_id', $request->property_type_id);
        }

        // ✅ Property Sub Type (NEW)
        if ($request->filled('property_subtype_id')) {
            $query->where('property_subtype_id', $request->property_subtype_id);
        }

        // ✅ Purpose (sale / rent / project)
        if ($request->filled('purpose')) {
            $query->where('purpose', $request->purpose);
        }

        // Price Range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Area Range (covered area)
        if ($request->filled('area_min')) {
            $query->where('area_size', '>=', $request->area_min);
        }
        if ($request->filled('area_max')) {
            $query->where('area_size', '<=', $request->area_max);
        }

        // Bedrooms
        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', '>=', (int)$request->bedrooms);
        }

        // Bathrooms
        if ($request->filled('bathrooms')) {
            $query->where('bathrooms', '>=', (int)$request->bathrooms);
        }

        // Featured
        if ($request->filled('is_featured')) {
            $query->where('is_featured', 1);
        }

        // ✅ Safe Status Filter (published / active)
        if (Schema::hasColumn('properties', 'status')) {
            $query->whereIn('status', ['active', 'published']);
        }

        // Keyword Search
        if ($request->filled('keyword')) {
            $kw = $request->keyword;
            $query->where(function($q) use ($kw) {
                $q->where('title', 'like', "%{$kw}%")
                  ->orWhere('description', 'like', "%{$kw}%")
                  ->orWhere('location', 'like', "%{$kw}%");
            });
        }

        /* =========================
           SORTING (Zameen Style)
        ==========================*/
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                default:
                    $query->orderBy('is_featured', 'desc')
                          ->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('is_featured', 'desc')
                  ->orderBy('created_at', 'desc');
        }

        $paginator = $query->paginate($perPage)->withQueryString();

        $data = $paginator->getCollection()->transform(function($property) {
            return [
                'id' => $property->id,
                'title' => $property->title,
                'description' => $property->description,
                'price' => $property->price,
                'city' => $property->city?->name,
                'area' => $property->area,
                'bedrooms' => $property->bedrooms,
                'bathrooms' => $property->bathrooms,
                'purpose' => $property->purpose ?? null,
                'is_featured' => (bool)$property->is_featured,
                'status' => $property->status,
                'property_type' => $property->propertyType?->name,
                'main_image' => $property->images->first()?->url
                    ?? (isset($property->images[0]) ? asset('storage/'.$property->images[0]->image_path) : null),
                'images' => $property->images->map(function($img){
                    return $img->url ?? asset('storage/'.$img->image_path);
                })->toArray(),
                'amenities' => $property->amenities->pluck('name')->toArray(),
                'features' => $property->features,
                'agent' => $property->user ? [
                    'id' => $property->user->id,
                    'name' => $property->user->name,
                ] : null,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $data,
            'meta' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
            ]
        ]);
    }

    /* 🔽 BAAQI FUNCTIONS BILKUL SAME REHNE DIYE 🔽 */

    public function show($id)
{
    $property = Property::with([
        'city',
        'propertyType',
        'images',
        'amenities',
        'features',
        'user',
        'user.agency'  // if agent belongs to agency
    ])->find($id);

    if (!$property) {
        return response()->json(['status'=>false,'message'=>'Not found'],404);
    }

    $viewed = session()->get('viewed_properties', []);
    array_unshift($viewed, $property->id);
    session()->put('viewed_properties', array_slice(array_unique($viewed), 0,10));

    $related = Property::where('id','!=',$property->id)
        ->where('city_id',$property->city_id)
        ->where('property_type_id',$property->property_type_id)
        ->whereBetween('price', [
            $property->price * 0.8,
            $property->price * 1.2
        ])
        ->limit(8)
        ->with('images')
        ->get()
        ->map(fn($p)=>[
            'id'=>$p->id,
            'title'=>$p->title,
            'price'=>$p->price,
            'main_image'=>$p->images->first()?->url,
        ]);

    return response()->json([
        'status'=>true,
        'data'=>[
            'id'=>$property->id,
            'title'=>$property->title,
            'description'=>$property->description,
            'price'=>$property->price,
            'price_per_unit' => $property->area_size > 0 ? round($property->price / $property->area_size,2) : null,
            'purpose'=>$property->purpose,
            'city'=>$property->city?->name,
            'area'=>$property->area,
            'area_size'=>$property->area_size,
            'area_unit'=>$property->area_unit,
            'location'=>[
                'lat'=>$property->latitude,
                'lng'=>$property->longitude,
            ],
            'type'=>$property->propertyType?->name,
            'images'=>$property->images->map(fn($img)=>$img->url)->toArray(),
            'amenities'=>$property->amenities->pluck('name'),
            'features'=>$property->features,
            'agent'=>[
                'id'=>$property->user?->id,
                'name'=>$property->user?->name,
                'phone'=>$property->user?->phone,
                'email'=>$property->user?->email,
                'agency'=> $property->user->agency?->name ?? null,
            ],
        ],
        'similar_properties'=>$related
    ]);
}

/**
 * GET /api/seo/properties
 * Zameen.com style SEO listing
 */
public function seoListing(Request $request)
{
    $perPage = (int) $request->get('per_page', 12);

    $query = Property::with(['city', 'propertyType', 'images']);

    // Purpose (sale / rent)
    if ($request->filled('purpose')) {
        $query->where('purpose', $request->purpose);
    }

    // City by ID
    if ($request->filled('city_id')) {
        $query->where('city_id', $request->city_id);
    }

    // Property Type
    if ($request->filled('property_type_id')) {
        $query->where('property_type_id', $request->property_type_id);
    }

    // Only active properties
    $query->whereIn('status', ['active', 'published']);

    $query->orderBy('is_featured', 'desc')
          ->orderBy('created_at', 'desc');

    $properties = $query->paginate($perPage);

    $data = $properties->map(function ($p) {
        return [
            'id' => $p->id,
            'title' => $p->title,
            'price' => $p->price,
            'city' => $p->city?->name,
            'property_type' => $p->propertyType?->name,
            'main_image' => $p->images->first()?->url
                ?? (isset($p->images[0]) ? asset('storage/'.$p->images[0]->image_path) : null),
        ];
    });

    return response()->json([
        'status' => true,
        'data' => $data,
        'meta' => [
            'total' => $properties->total(),
            'current_page' => $properties->currentPage(),
            'last_page' => $properties->lastPage(),
        ]
    ]);
}

public function store(Request $request)
{
    $data = $request->validate([
        'title' => 'required',
        'description' => 'required',
        'city_id' => 'required',
        'area' => 'required',
        'price' => 'required|numeric',
        'bedrooms' => 'nullable|integer',
        'bathrooms' => 'nullable|integer',
        'property_type_id' => 'required',
        'purpose' => 'required|in:sale,rent',
    ]);

    $data['user_id'] = $request->user()->id;
    $data['status'] = 'pending';

    $property = Property::create($data);

    return response()->json([
        'status' => true,
        'message' => 'Property submitted successfully',
        'data' => $property
    ]);
}


public function showBySlug($slug)
{
    $property = Property::where('slug', $slug)
        ->with(['city','images','amenities','features','user'])
        ->firstOrFail();

    return response()->json([
        'status' => true,
        'data' => $property
    ]);
}



    public function priceRange() { /* same */ }
    public function areaRange() { /* same */ }
    public function beds() { /* same */ }
    public function getTypes() { /* same */ }
    public function getSubTypes($type_id) { /* same */ }
}
