<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PropertyImage;

class PropertyImageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'images.*' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $uploaded = [];

        if ($request->hasFile('images')) {

            foreach ($request->file('images') as $image) {

                $path = $image->store('properties','public');

                $img = PropertyImage::create([
                    'property_id' => $request->property_id,
                    'image_path' => $path
                ]);

                $uploaded[] = asset('storage/'.$path);
            }
        }

        return response()->json([
            'status' => true,
            'images' => $uploaded
        ]);
    }
}