<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Property;
use Illuminate\Http\Request;

class FeaturePropertyController extends Controller
{
    public function feature(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'property_id' => 'required|exists:properties,id'
        ]);

        $invoice = Invoice::where('id', $request->invoice_id)
            ->where('user_id', auth()->id())
            ->where('status', 'paid')
            ->firstOrFail();

        $property = Property::where('id', $request->property_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $days = $invoice->package->feature_days;

        $property->update([
            'is_featured' => 1,
            'featured_until' => now()->addDays($days)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Property featured successfully'
        ]);
    }
}
