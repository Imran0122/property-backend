<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['index', 'show', 'update']);
    }

    /**
     * Store a new lead (Contact Agent form)
     */
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'name'        => 'required|string|max:255',
            'email'       => 'nullable|email|max:255',
            'phone'       => 'nullable|string|max:20',
            'message'     => 'nullable|string|max:1000',
        ]);

        $property = Property::findOrFail($request->property_id);

        $lead = Lead::create([
            'property_id' => $property->id,
            'agent_id'    => $property->user_id,
            'user_id'     => Auth::id(),
            'name'        => $request->name,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'message'     => $request->message,
            'status'      => 'new', // default status
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Your inquiry has been sent successfully!']);
        }

        return back()->with('success', '✅ Your inquiry has been sent!');
    }

    /**
     * Agent can view all leads for their properties
     */
    public function index()
    {
        $user = Auth::user();
        $leads = Lead::with(['property'])
            ->where('agent_id', $user->id)
            ->latest()
            ->paginate(20);

        return view('leads.index', compact('leads'));
    }

    /**
     * Show details of a specific lead
     */
    public function show(Lead $lead)
    {
        $user = Auth::user();
        if ($lead->agent_id !== $user->id) {
            abort(403, 'Unauthorized access');
        }

        return view('leads.show', compact('lead'));
    }

    /**
     * Update lead status (contacted / converted / not interested)
     */
    public function update(Request $request, Lead $lead)
    {
        $user = Auth::user();
        if ($lead->agent_id !== $user->id) {
            abort(403, 'Unauthorized action');
        }

        $request->validate([
            'status' => 'required|in:new,contacted,converted,not_interested',
        ]);

        $lead->update(['status' => $request->status]);

        return back()->with('success', '✅ Lead status updated successfully!');
    }
}
