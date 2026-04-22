<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ContactInquiry;
use Illuminate\Http\Request;

class ContactInquiryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'inquiry_type' => 'required|in:contact,developer,agent',
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'phone'        => 'required|string|max:30',
            'subject'      => 'nullable|string|max:255',
            'message'      => 'required|string',
            'source_page'  => 'nullable|string|max:255',
        ]);

        $inquiry = ContactInquiry::create([
            'inquiry_type' => $validated['inquiry_type'],
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'phone'        => $validated['phone'],
            'subject'      => $validated['subject'] ?? null,
            'message'      => $validated['message'],
            'source_page'  => $validated['source_page'] ?? null,
            'status'       => 'new',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Inquiry submitted successfully',
            'data'    => $inquiry,
        ], 201);
    }
}