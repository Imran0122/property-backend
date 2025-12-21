<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Property;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::count();
        $properties = Property::count();

        return view('admin.dashboard', compact('users', 'properties'));
    }
}
