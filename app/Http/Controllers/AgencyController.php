<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use Illuminate\Http\Request;

class AgencyController extends Controller
{
    public function index()
    {
        $agencies = Agency::withCount('agents', 'properties')->paginate(10);
        return view('agencies.index', compact('agencies'));
    }

    public function show(Agency $agency)
    {
        $agency->load('agents.user', 'properties');
        return view('agencies.show', compact('agency'));
    }
}
