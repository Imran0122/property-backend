<?php

public function handle($request, Closure $next)
{
    $user = auth()->user();

    if (!$user->subscription || !$user->subscription->isActive()) {
        return redirect()->route('packages.index')
            ->with('error', 'You need an active package to post properties.');
    }

    if ($user->subscription->used_properties >= $user->subscription->package->property_limit) {
        return redirect()->route('packages.index')
            ->with('error', 'Your property limit is reached. Upgrade package.');
    }

    return $next($request);
}
