<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Package;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id'
        ]);

        $package = Package::findOrFail($request->package_id);

        $invoice = Invoice::create([
            'user_id' => auth()->id(),
            'package_id' => $package->id,
            'amount' => $package->price,
            'status' => 'unpaid'
        ]);

        return response()->json([
            'status' => true,
            'invoice_id' => $invoice->id
        ]);
    }
}
