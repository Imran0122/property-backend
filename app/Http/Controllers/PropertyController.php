<?php 

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\City;
use App\Models\PropertyType;
use App\Models\Amenity;
use App\Models\PropertyImage;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class PropertyController extends Controller
{
   public function __construct()
{
    // Fix for undefined method error (middleware not available in some boot contexts)
    if (method_exists($this, 'middleware')) {
        $this->middleware('auth')->except(['index', 'show', 'searchApi']);
    }
}


    /**
     * Display properties with filters (Web)
     */
    public function index(Request $request)
    {
        $query = Property::with(['city', 'propertyType', 'amenities', 'images', 'user'])
            ->latest();

        // 🔎 Filters
        if ($request->filled('city_id')) $query->where('city_id', $request->city_id);
        if ($request->filled('property_type_id')) $query->where('property_type_id', $request->property_type_id);
        if ($request->filled('min_price')) $query->where('price', '>=', $request->min_price);
        if ($request->filled('max_price')) $query->where('price', '<=', $request->max_price);
        if ($request->filled('bedrooms')) $query->where('bedrooms', '>=', $request->bedrooms);
        if ($request->filled('bathrooms')) $query->where('bathrooms', '>=', $request->bathrooms);
        if ($request->filled('keyword')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->keyword . '%')
                  ->orWhere('location', 'like', '%' . $request->keyword . '%');
            });
        }
        if ($request->filled('status')) $query->where('status', $request->status);

        $properties = $query->paginate(12)->withQueryString();
        $cities = City::orderBy('name')->get();
        $types  = PropertyType::orderBy('name')->get();

        return view('properties.index', compact('properties', 'cities', 'types'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $cities    = City::orderBy('name')->get();
        $types     = PropertyType::orderBy('name')->get();
        $amenities = Amenity::orderBy('name')->get();

        return view('properties.create', compact('cities', 'types', 'amenities'));
    }

    /**
     * Store new property (Web flow)
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'location'         => 'required|string|max:255',
            'price'            => 'required|numeric',
            'city_id'          => 'required|exists:cities,id',
            'property_type_id' => 'required|exists:property_types,id',
            'images.*'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $property = Property::create([
            'user_id'          => Auth::id(),
            'title'            => $request->title,
            'description'      => $request->description,
            'location'         => $request->location,
            'city_id'          => $request->city_id,
            'area'             => $request->area,
            'bedrooms'         => $request->bedrooms,
            'bathrooms'        => $request->bathrooms,
            'price'            => $request->price,
            'status'           => 'pending', // 🟢 Admin approval flow
            'is_featured'      => $request->boolean('is_featured'),
            'property_type_id' => $request->property_type_id,
        ]);

        // ✅ Attach amenities
        $property->amenities()->sync($request->amenities ?? []);

        // ✅ Upload images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $img) {
                $path = $img->store('properties', 'public');
                $property->images()->create([
                    'image_path' => $path,
                    'is_primary' => $index === 0 ? 1 : 0
                ]);
            }
        }

        return redirect()->route('properties.index')
            ->with('success', '✅ Property submitted! Pending admin approval.');
    }

    /**
     * Show property details
     */
   public function show(Property $property)
{
    $property->load(['images', 'amenities', 'user']);

    // Similar properties - same type and location, except current one
    $similar = Property::with('images')
        ->where('id', '!=', $property->id)
        ->where('type_id', $property->type_id)
        ->where('location', $property->location)
        ->take(4)
        ->get();

    return view('properties.show', compact('property', 'similar'));
}


    /**
     * Show edit form
     */
    public function edit(Property $property)
    {
        $cities    = City::orderBy('name')->get();
        $types     = PropertyType::orderBy('name')->get();
        $amenities = Amenity::orderBy('name')->get();

        $property->load('amenities');

        return view('properties.edit', compact('property', 'cities', 'types', 'amenities'));
    }

    /**
     * Update property (Web flow)
     */
    public function update(Request $request, Property $property)
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'location'         => 'required|string|max:255',
            'price'            => 'required|numeric',
            'city_id'          => 'required|exists:cities,id',
            'property_type_id' => 'required|exists:property_types,id',
        ]);

        $property->update($request->only([
            'title','description','location','city_id','area',
            'bedrooms','bathrooms','price','status','is_featured','property_type_id'
        ]));

        // ✅ Sync amenities
        $property->amenities()->sync($request->amenities ?? []);

        // ✅ New images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('properties', 'public');
                $property->images()->create(['image_path' => $path]);
            }
        }

        return redirect()->route('properties.index')
            ->with('success', '✅ Property updated successfully.');
    }




    
    /**
     * Delete property (Web flow)
     */
    public function destroy(Property $property)
    {
        foreach ($property->images as $img) {
            Storage::disk('public')->delete($img->image_path);
            $img->delete();
        }
        $property->delete();

        return redirect()->route('properties.index')
            ->with('success', '❌ Property deleted successfully.');
    }

    /**
     * 🟢 Delete single image
     */
    public function deleteImage(PropertyImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return response()->json(['success' => true]);
    }

    /**
     * 🟢 Set primary image
     */
    public function setPrimaryImage(Property $property, PropertyImage $image)
    {
        $property->images()->update(['is_primary' => 0]);
        $image->update(['is_primary' => 1]);

        return response()->json(['success' => true]);
    }

    /**
     * 🟢 Toggle favorite property
     */
    public function toggleFavorite(Property $property)
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($user->favorites()->where('property_id', $property->id)->exists()) {
            $user->favorites()->detach($property->id);
            return response()->json(['is_favorite' => false]);
        } else {
            $user->favorites()->attach($property->id);
            return response()->json(['is_favorite' => true]);
        }
    }

    /**
     * 🟢 Toggle Featured
     */
    public function toggleFeatured(Property $property)
    {
        $property->update(['is_featured' => !$property->is_featured]);
        return back()->with('success','Featured status updated!');
    }

    /**
     * 🟢 Contact Agent / Store Lead
     */
    public function contactAgent(Request $request, Property $property)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        Lead::create([
            'user_id'     => Auth::id(),
            'property_id' => $property->id,
            'agent_id'    => $property->user_id,
            'message'     => $request->message,
        ]);

        return back()->with('success','Your message has been sent to the agent!');
    }

    /* -----------------------------------------------------------------
     | 🚀 API Endpoints (JSON)
     |-----------------------------------------------------------------*/

    public function storeApi(Request $request)
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'location'         => 'required|string|max:255',
            'price'            => 'required|numeric',
            'city_id'          => 'required|exists:cities,id',
            'property_type_id' => 'required|exists:property_types,id',
        ]);

        $property = Property::create([
            'user_id'          => $request->user()->id,
            'title'            => $request->title,
            'description'      => $request->description,
            'location'         => $request->location,
            'city_id'          => $request->city_id,
            'area'             => $request->area,
            'bedrooms'         => $request->bedrooms,
            'bathrooms'        => $request->bathrooms,
            'price'            => $request->price,
            'status'           => 'pending',
            'is_featured'      => $request->boolean('is_featured'),
            'property_type_id' => $request->property_type_id,
        ]);

        return response()->json(['message' => 'Property added successfully', 'property' => $property]);
    }

    public function updateApi(Request $request, $id)
    {
        $property = Property::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $property->update($request->only([
            'title','description','location','city_id','area','bedrooms',
            'bathrooms','price','status','is_featured','property_type_id'
        ]));

        return response()->json(['message' => 'Property updated successfully', 'property' => $property]);
    }

    public function destroyApi(Request $request, $id)
    {
        $property = Property::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $property->delete();

        return response()->json(['message' => 'Property deleted successfully']);
    }

    public function searchApi(Request $request)
    {
        $query = Property::with(['city', 'propertyType', 'amenities', 'images', 'user'])->latest();

        if ($request->filled('city_id')) $query->where('city_id', $request->city_id);
        if ($request->filled('property_type_id')) $query->where('property_type_id', $request->property_type_id);
        if ($request->filled('min_price')) $query->where('price', '>=', $request->min_price);
        if ($request->filled('max_price')) $query->where('price', '<=', $request->max_price);
        if ($request->filled('bedrooms')) $query->where('bedrooms', '>=', $request->bedrooms);
        if ($request->filled('bathrooms')) $query->where('bathrooms', '>=', $request->bathrooms);
        if ($request->filled('keyword')) {
            $query->where(function($q) use ($request) {
                $q->where('title','like','%'.$request->keyword.'%')
                  ->orWhere('location','like','%'.$request->keyword.'%');
            });
        }
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('min_area')) $query->where('area','>=',$request->min_area);
        if ($request->filled('max_area')) $query->where('area','<=',$request->max_area);
        if ($request->filled('amenities')) {
            $query->whereHas('amenities', function($q) use ($request) {
                $q->whereIn('amenities.id', (array)$request->amenities);
            });
        }
        if ($request->filled('sort_by')) {
            switch ($request->sort_by) {
                case 'price_low_high': $query->orderBy('price','asc'); break;
                case 'price_high_low': $query->orderBy('price','desc'); break;
                case 'oldest': $query->orderBy('created_at','asc'); break;
                default: $query->orderBy('created_at','desc');
            }
        }

        return response()->json($query->paginate(12)->withQueryString());
    }
}
