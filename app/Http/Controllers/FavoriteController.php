<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Property;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggle(Property $property)
    {
        $user = auth()->user();

        $favorite = Favorite::where('user_id', $user->id)
                            ->where('property_id', $property->id)
                            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['is_favorite' => false]);
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'property_id' => $property->id,
            ]);
            return response()->json(['is_favorite' => true]);
        }
    }

    public function index()
    {
        $favorites = Favorite::with('property')->where('user_id', auth()->id())->paginate(12);
        return view('favorites.index', compact('favorites'));
    }
}
