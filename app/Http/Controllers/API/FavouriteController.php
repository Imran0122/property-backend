<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Favourite;
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
            'message' => $fav->wasRecentlyCreated
                ? 'Property added to favourites'
                : 'Property already exists in favourites'
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
        $favourites = Favourite::with([
                'property.images',
                'property.city',
                'property.areaDetail',
                'property.propertyType',
            ])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $favourites
                ->filter(fn ($fav) => $fav->property)
                ->map(function ($fav) {
                    $p = $fav->property;
                    $mainImage = optional($p->images->sortByDesc('is_primary')->first())->url;

                    return [
                        'id' => $p->id,
                        'slug' => $p->slug,
                        'title' => $p->title,
                        'price' => (float) $p->price,
                        'currency' => 'MAD',
                        'city' => $p->city?->name,
                        'area_name' => $p->areaDetail?->name ?: $p->area,
                        'location_text' => collect([
                            $p->areaDetail?->name ?: $p->area,
                            $p->city?->name,
                        ])->filter()->implode(', '),
                        'property_type' => $p->propertyType?->name,
                        'main_image' => $mainImage,
                        'images_count' => $p->images->count(),
                    ];
                })
                ->values()
        ]);
    }
}