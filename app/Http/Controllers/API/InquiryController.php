<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    /**
     * POST /api/property-inquiry
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'name'        => 'required|string|max:255',
            'email'       => 'required|email',
            'phone'       => 'required|string|max:30',
            'message'     => 'nullable|string',
        ]);

        Inquiry::create([
            'property_id' => $validated['property_id'],
            'user_id'     => auth()->id(),
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'phone'       => $validated['phone'],
            'message'     => $validated['message'] ?? '',
            'status'      => 'new',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Your inquiry has been sent successfully'
        ]);
    }
}
