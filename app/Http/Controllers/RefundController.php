<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\InvoiceService;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    public function refund(Request $request, $invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);

        // 1. Call Stripe/PayPal API to issue refund (example only)
        // $refund = Stripe::refund($invoice->payment_id);
        
        $invoice->update([
            'status' => 'refunded',
            'refund_id' => 'RFD' . uniqid(),
            'refunded_at' => now()
        ]);

        // 2. Generate Refund Invoice
        $filePath = InvoiceService::generate($invoice);

        // 3. Send refund invoice email
        Mail::to($invoice->user->email)->send(new InvoiceMail($invoice, $filePath));

        return response()->json([
            'message' => 'Refund processed and invoice sent to user.',
            'invoice' => $invoice
        ]);
    }
}
