<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Favourite;
use App\Models\Property;
use Illuminate\Http\Request;

class FavouriteController extends Controller
{
    /**
     * POST /api/favourites
     */
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id'
        ]);

        $fav = Favourite::firstOrCreate([
            'user_id' => auth()->id(),
            'property_id' => $request->property_id
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Property added to favourites'
        ]);
    }

    /**
     * DELETE /api/favourites/{property_id}
     */
    public function destroy($property_id)
    {
        Favourite::where('user_id', auth()->id())
            ->where('property_id', $property_id)
            ->delete();

        return response()->json([
            'status' => true,
            'message' => 'Property removed from favourites'
        ]);
    }

    /**
     * GET /api/my-favourites
     */
    public function myFavourites()
    {
        $favourites = Favourite::where('user_id', auth()->id())
            ->with('property.images')
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $favourites->map(function ($fav) {
                $p = $fav->property;
                return [
                    'id' => $p->id,
                    'title' => $p->title,
                    'price' => $p->price,
                    'main_image' => $p->images->first()?->url
                        ?? (isset($p->images[0]) ? asset('storage/'.$p->images[0]->image_path) : null),
                ];
            })
        ]);
    }
}
