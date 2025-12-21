<?php

class SubscriptionController extends Controller
{
    public function index()
    {
        $packages = Package::all();
        return view('subscriptions.index', compact('packages'));
    }

    public function purchase(Request $request, Package $package)
    {
        $subscription = Subscription::create([
            'user_id' => auth()->id(),
            'package_id' => $package->id,
            'starts_at' => now(),
            'ends_at' => now()->addDays($package->duration_days),
            'status' => 'active',
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Package purchased successfully!');
    }
}
