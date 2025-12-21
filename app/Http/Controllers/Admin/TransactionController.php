<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Invoice;
use Illuminate\Http\Request;
use PDF; // barryvdh/laravel-dompdf package use karna hoga

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('user','package')->latest()->paginate(20);
        return view('admin.transactions.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load('user','package','invoice');
        return view('admin.transactions.show', compact('transaction'));
    }

    public function refund(Transaction $transaction)
    {
        // ✅ Step 1: Trigger actual payment gateway refund (Stripe/PayPal SDK code yahan aayega)

        // ✅ Step 2: Mark transaction refunded in DB
        $transaction->update(['status' => 'refunded']);

        // ✅ Step 3: Update invoice status also
        if ($transaction->invoice) {
            $transaction->invoice->update(['status' => 'refunded']);
        }

        return back()->with('success','Transaction marked as refunded. (SDK refund integration required)');
    }

    public function generateInvoice(Transaction $transaction)
    {
        // ✅ Generate unique invoice number
        $invoiceNumber = 'INV-'.time().'-'.$transaction->id;

        // ✅ Create invoice record
        $invoice = Invoice::create([
            'transaction_id' => $transaction->id,
            'invoice_number' => $invoiceNumber,
            'amount' => $transaction->amount,
            'currency' => $transaction->currency ?? 'PKR',
            'status' => 'issued',
        ]);

        // ✅ Generate PDF (using barryvdh/laravel-dompdf)
        $pdf = PDF::loadView('admin.transactions.invoice_pdf', compact('transaction','invoice'));
        $filePath = 'invoices/'.$invoiceNumber.'.pdf';
        \Storage::disk('public')->put($filePath, $pdf->output());

        // ✅ Save PDF path in invoice
        $invoice->update(['pdf_path' => $filePath]);

        return back()->with('success','Invoice generated successfully.');
    }

    public function downloadInvoice(Invoice $invoice)
    {
        if (!$invoice->pdf_path || !\Storage::disk('public')->exists($invoice->pdf_path)) {
            return back()->with('error','Invoice file not found.');
        }

        return response()->download(storage_path('app/public/'.$invoice->pdf_path));
    }
}
