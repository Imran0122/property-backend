<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SavedProperty;
use Illuminate\Http\Request;

class SavedPropertyController extends Controller
{
    public function toggle(Request $request)
    {
        $saved = SavedProperty::where('user_id', auth()->id())
            ->where('property_id', $request->property_id)
            ->first();

        if ($saved) {
            $saved->delete();
            return response()->json(['saved' => false]);
        }

        SavedProperty::create([
            'user_id' => auth()->id(),
            'property_id' => $request->property_id
        ]);

        return response()->json(['saved' => true]);
    }

    public function index()
    {
        return auth()->user()->savedProperties()->with('property')->get();
    }
}
