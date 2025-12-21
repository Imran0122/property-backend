<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\PropertyInquiry;

class InquiryController extends Controller
{
    /**
     * GET /api/admin/inquiries
     */
    public function index()
    {
        $inquiries = PropertyInquiry::with('property', 'agent')
            ->latest()
            ->paginate(20);

        return response()->json([
            'status' => true,
            'data' => $inquiries
        ]);
    }
}
