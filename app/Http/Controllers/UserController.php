<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function favorites()
    {
        $properties = auth()->user()->favorites()->paginate(12);
        return view('users.favorites', compact('properties'));
    }
}
