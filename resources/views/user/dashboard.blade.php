@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 fw-bold">My Dashboard</h2>

    {{-- ✅ Stats Cards --}}
    <div class="row text-center mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h4>{{ $stats['favorites'] }}</h4>
                <p class="text-muted">Favorite Properties</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h4>{{ $stats['savedSearches'] }}</h4>
                <p class="text-muted">Saved Searches</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h4>{{ $stats['leads'] }}</h4>
                <p class="text-muted">My Leads</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h4>{{ $stats['myProperties'] }}</h4>
                <p class="text-muted">My Properties</p>
            </div>
        </div>
    </div>

    {{-- ✅ Favorites --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Favorite Properties</h5>
            <table class="table">
                <thead><tr><th>Title</th><th>Price</th><th>Date</th></tr></thead>
                <tbody>
                    @foreach($favorites as $fav)
                        <tr>
                            <td>{{ $fav->property->title ?? 'N/A' }}</td>
                            <td>PKR {{ number_format($fav->property->price ?? 0) }}</td>
                            <td>{{ $fav->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ✅ Saved Searches --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Saved Searches</h5>
            <table class="table">
                <thead><tr><th>Filters</th><th>Date</th></tr></thead>
                <tbody>
                    @foreach($savedSearches as $search)
                        <tr>
                            <td>{{ $search->filters ?? 'N/A' }}</td>
                            <td>{{ $search->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ✅ My Leads --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3">My Leads</h5>
            <table class="table">
                <thead><tr><th>Property</th><th>Message</th><th>Date</th></tr></thead>
                <tbody>
                    @foreach($leads as $lead)
                        <tr>
                            <td>{{ $lead->property->title ?? 'N/A' }}</td>
                            <td>{{ Str::limit($lead->message, 40) }}</td>
                            <td>{{ $lead->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ✅ My Properties --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3">My Properties</h5>
            <table class="table">
                <thead><tr><th>Title</th><th>Price</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                    @foreach($myProperties as $p)
                        <tr>
                            <td>{{ $p->title }}</td>
                            <td>PKR {{ number_format($p->price) }}</td>
                            <td>{{ ucfirst($p->status) }}</td>
                            <td>{{ $p->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
