<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Package;

class PackageController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => Package::where('status', 'active')->get()
        ]);
    }
}
