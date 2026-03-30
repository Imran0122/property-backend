<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PropertyInquiry;
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
            'email'       => 'required|email|max:255',
            'phone'       => 'required|string|max:30',
            'message'     => 'nullable|string',
        ]);

        PropertyInquiry::create([
            'property_id' => $validated['property_id'],
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'phone'       => $validated['phone'],
            'message'     => $validated['message'] ?? '',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Your inquiry has been sent successfully',
        ], 201);
    }
}