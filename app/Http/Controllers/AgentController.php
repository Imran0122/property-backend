<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\Lead;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AgentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ✅ Dashboard overview
    public function index()
    {
        $agentId = Auth::id();

        $totalProperties = Property::where('user_id', $agentId)->count();
        $activeProperties = Property::where('user_id', $agentId)->where('status', 'active')->count();
        $pendingProperties = Property::where('user_id', $agentId)->where('status', 'pending')->count();
        $leadsCount = Lead::whereHas('property', fn($q) => $q->where('user_id', $agentId))->count();
        $newLeads = Lead::whereHas('property', fn($q) => $q->where('user_id', $agentId))->where('status', 'new')->count();

        return view('agent.dashboard', compact(
            'totalProperties', 'activeProperties', 'pendingProperties', 'leadsCount', 'newLeads'
        ));
    }

    // ✅ Agent properties list
    public function properties(Request $request)
    {
        $agentId = Auth::id();
        $properties = Property::with('images')
            ->where('user_id', $agentId)
            ->latest()
            ->paginate(12);

        return view('agent.properties.index', compact('properties'));
    }

    // ✅ Show edit page
    public function editProperty(Property $property)
    {
        $this->authorize('update', $property);
        $property->load('images', 'amenities');
        return view('agent.properties.edit', compact('property'));
    }

    // ✅ Update property status (AJAX)
    public function updatePropertyStatus(Request $request, Property $property)
    {
        $this->authorize('update', $property);
        $request->validate(['status' => 'required|in:pending,active,sold,rented']);
        $property->update(['status' => $request->status]);
        return response()->json(['success' => true, 'status' => $property->status]);
    }

    // ✅ Set primary image (AJAX)
    public function setPrimaryImage(Request $request, Property $property)
    {
        $this->authorize('update', $property);
        $request->validate(['image_id' => 'required|integer|exists:property_images,id']);

        $property->images()->update(['is_primary' => false]);

        $img = PropertyImage::where('id', $request->image_id)->where('property_id', $property->id)->firstOrFail();
        $img->is_primary = true;
        $img->save();

        return response()->json(['success' => true, 'image_id' => $img->id]);
    }

    // ✅ Delete image
    public function deleteImage(PropertyImage $image)
    {
        $this->authorize('update', $image->property);
        Storage::disk('public')->delete($image->image_path);
        $image->delete();
        return response()->json(['success' => true]);
    }

    // ✅ Leads list for this agent
    public function leads()
    {
        $agentId = Auth::id();
        $leads = Lead::with('property', 'user')
            ->whereHas('property', fn($q) => $q->where('user_id', $agentId))
            ->latest()
            ->paginate(20);

        return view('agent.leads.index', compact('leads'));
    }

    // ✅ Show single lead
    public function showLead(Lead $lead)
    {
        $this->authorize('view', $lead);
        $lead->load('property', 'user');
        return view('agent.leads.show', compact('lead'));
    }

    // ✅ Mark lead contacted
    public function markLeadContacted(Lead $lead)
    {
        $this->authorize('view', $lead);
        $lead->update(['status' => 'contacted']);
        return response()->json(['success' => true, 'status' => 'contacted']);
    }

    // ✅ Agent profile (View + Update)
    public function profile()
    {
        $agent = Auth::user();
        return view('agent.profile', compact('agent'));
    }

    public function updateProfile(Request $request)
    {
        $agent = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'about' => 'nullable|string|max:1000',
        ]);

        $agent->update($request->only('name', 'phone', 'about'));

        return redirect()->route('agent.profile')->with('success', 'Profile updated successfully.');
    }

    // ✅ Reports submitted against agent's properties
    public function reports()
    {
        $agentId = Auth::id();

        $reports = Report::with('property')
            ->whereHas('property', fn($q) => $q->where('user_id', $agentId))
            ->latest()
            ->paginate(15);

        return view('agent.reports.index', compact('reports'));
    }
}
