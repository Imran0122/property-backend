<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Wallet;

class FeatureController extends Controller
{
    public function feature(Request $request, $id)
    {
        $property = Property::findOrFail($id);
        $wallet = Wallet::firstOrCreate(['user_id' => auth()->id()]);
        $featureCost = 100; // credits required
        $durationDays = 7; // days valid

        if ($wallet->balance < $featureCost) {
            return back()->with('error', 'Insufficient credits! Please recharge your wallet.');
        }

        // Deduct credits
        $wallet->debit($featureCost, "Featured property #{$property->id}");

        // Update property
        $property->update([
            'is_featured' => true,
            'featured_until' => now()->addDays($durationDays),
        ]);

        return back()->with('success', "Property has been featured for {$durationDays} days!");
    }

    // Auto-expire featured listings (can be run by scheduler)
    public function expireFeatured()
    {
        Property::where('is_featured', true)
            ->where('featured_until', '<', now())
            ->update(['is_featured' => false, 'featured_until' => null]);
    }
}
