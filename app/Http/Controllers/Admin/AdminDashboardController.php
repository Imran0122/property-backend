<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $properties = Property::with('user', 'city', 'propertyType')->latest()->paginate(10);
        $users = User::count();

        return view('admin.dashboard', compact('properties', 'users'));
    }
}
