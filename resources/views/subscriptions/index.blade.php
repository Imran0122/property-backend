@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2>Choose a Package</h2>

    <div class="row">
        @foreach($packages as $package)
        <div class="col-md-4 mb-4">
            <div class="card p-3 h-100">
                <h4>{{ $package->name }}</h4>
                <p class="text-muted">{{ $package->property_limit }} properties • {{ $package->featured_limit }} featured • {{ $package->duration_days }} days</p>
                <h3>PKR {{ number_format($package->price) }}</h3>

                <form method="POST" action="{{ route('payments.stripe.checkout', $package) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">Pay with Card (Stripe)</button>
                </form>

                <form method="POST" action="{{ route('payments.paypal.create', $package) }}" class="d-inline ms-2">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary">Pay with PayPal</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
