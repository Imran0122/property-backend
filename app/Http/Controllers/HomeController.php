<?php

namespace App\Http\Controllers;

use App\Models\Property;

class HomeController extends Controller
{
    public function index()
    {
        // Fetch featured properties (you can change the logic later)
        $featuredProperties = Property::with(['city', 'images'])
            ->where('status', 'active')
            ->take(6)
            ->get();

        // Send this data to the view
        return view('home', [
            'featuredProperties' => $featuredProperties,
        ]);
    }
}
