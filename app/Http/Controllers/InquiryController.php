<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Property;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    /**
     * Store a new inquiry
     */
    public function store(Request $request, $propertyId)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'nullable|string|max:1000',
        ]);

        Inquiry::create([
            'property_id' => $propertyId,
            'user_id' => auth()->id(),
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'message' => $request->message,
        ]);

        return redirect()->back()->with('success', 'Your inquiry has been sent successfully.');
    }

    /**
     * Admin: Show all inquiries
     */
    public function index()
    {
        $inquiries = Inquiry::with(['property', 'user'])->latest()->paginate(15);
        return view('admin.inquiries.index', compact('inquiries'));
    }
}
