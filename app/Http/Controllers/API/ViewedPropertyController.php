<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class ViewedPropertyController extends Controller
{
    public function index(Request $request)
    {
        $viewedIds = session()->get('viewed_properties', []);

        if (empty($viewedIds)) {
            return response()->json([]);
        }

        $properties = Property::with(['city', 'images'])
            ->whereIn('id', $viewedIds)
            ->where('status', 'active')
            ->latest()
            ->limit(10)
            ->get();

        return response()->json(
            $properties->map(fn ($p) => [
                'id' => $p->id,
                'title' => $p->title,
                'price' => $p->price,
                'city' => optional($p->city)->name,
                'main_image' => $p->main_image,
                'area' => $p->area,
                'bedrooms' => $p->bedrooms,
            ])
        );
    }
}
