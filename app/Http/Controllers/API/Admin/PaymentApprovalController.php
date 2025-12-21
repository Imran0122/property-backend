<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Property;

class PaymentApprovalController extends Controller
{
    public function approve($invoiceId)
    {
        $invoice = Invoice::with('package')->findOrFail($invoiceId);

        $invoice->update([
            'status' => 'paid'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Payment approved'
        ]);
    }
}
