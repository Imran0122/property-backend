{{-- resources/views/search.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- Filters Bar --}}
    <form method="GET" action="{{ route('search') }}" class="row g-3 mb-4">
        <div class="col-md-3">
            <select name="city_id" class="form-control">
                <option value="">Select City</option>
                @foreach($cities as $city)
                    <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                        {{ $city->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <select name="property_type_id" class="form-control">
                <option value="">Property Type</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}" {{ request('property_type_id') == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <input type="number" name="min_price" class="form-control" placeholder="Min Price" value="{{ request('min_price') }}">
        </div>
        <div class="col-md-2">
            <input type="number" name="max_price" class="form-control" placeholder="Max Price" value="{{ request('max_price') }}">
        </div>

        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-success">Search</button>
        </div>
    </form>

    {{-- Sorting + View Options --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <strong>{{ $properties->total() }}</strong> Properties Found
        </div>
        <div class="d-flex">
            <select class="form-select me-2" onchange="location = this.value;">
                <option value="{{ request()->fullUrlWithQuery(['sort'=>'newest']) }}" {{ request('sort')=='newest' ? 'selected' : '' }}>Newest</option>
                <option value="{{ request()->fullUrlWithQuery(['sort'=>'oldest']) }}" {{ request('sort')=='oldest' ? 'selected' : '' }}>Oldest</option>
                <option value="{{ request()->fullUrlWithQuery(['sort'=>'low']) }}" {{ request('sort')=='low' ? 'selected' : '' }}>Price: Low to High</option>
                <option value="{{ request()->fullUrlWithQuery(['sort'=>'high']) }}" {{ request('sort')=='high' ? 'selected' : '' }}>Price: High to Low</option>
            </select>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-secondary btn-sm active" id="gridViewBtn"><i class="bi bi-grid"></i></button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="listViewBtn"><i class="bi bi-list"></i></button>
            </div>
        </div>
    </div>

    <div class="row" id="propertyGrid">
        @forelse($properties as $property)
            <div class="col-md-4 mb-4 property-item">
                <div class="card h-100 shadow-sm">
                    <div class="position-relative">
                        <img src="{{ $property->images[0]->url ?? (isset($property->images[0]) ? asset('storage/'.$property->images[0]->image_path) : 'https://via.placeholder.com/400x250') }}" class="card-img-top" alt="property">

                        {{-- Badges --}}
                        <span class="badge bg-success position-absolute top-0 start-0 m-2">Verified</span>
                        @if($property->is_featured)
                            <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2">Featured</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <h5 class="fw-bold">
                            <a href="{{ route('properties.show', $property->id) }}" class="text-dark text-decoration-none">{{ $property->title }}</a>
                        </h5>
                        <p class="text-success fw-bold">PKR {{ number_format($property->price) }}</p>
                        <p class="text-muted small"><i class="bi bi-geo-alt"></i> {{ $property->location }}, {{ $property->city->name ?? '' }}</p>

                        {{-- List view hidden content --}}
                        <div class="list-view-only d-none">
                            <p>{{ Str::limit($property->description, 120) }}</p>
                        </div>
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-between">
                        <small>{{ $property->bedrooms }} Beds • {{ $property->bathrooms }} Baths • {{ $property->area }} Sq.ft</small>
                        @auth
                            <form method="POST" action="{{ route('properties.favorite', $property->id) }}">
                                @csrf
                                <button class="btn btn-outline-danger btn-sm"><i class="bi {{ auth()->user()->favorites->contains($property->id) ? 'bi-heart-fill' : 'bi-heart' }}"></i></button>
                            </form>
                        @endauth
                    </div>
                </div>
            </div>
        @empty
            <p>No properties found.</p>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $properties->links() }}
    </div>

    {{-- Map Section --}}
    <div class="mt-5">
        <h4>Properties on Map</h4>
        <div id="map" style="height:400px;"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Grid/List toggle
    document.getElementById('gridViewBtn').addEventListener('click', function(){
        document.querySelectorAll('.property-item').forEach(el => el.classList.add('col-md-4'));
        document.querySelectorAll('.list-view-only').forEach(el => el.classList.add('d-none'));
    });
    document.getElementById('listViewBtn').addEventListener('click', function(){
        document.querySelectorAll('.property-item').forEach(el => el.classList.remove('col-md-4'));
        document.querySelectorAll('.list-view-only').forEach(el => el.classList.remove('d-none'));
    });

    // Map with markers
    function initMap(){
        let map = new google.maps.Map(document.getElementById('map'), {
            zoom: 12,
            center: { lat: 31.5204, lng: 74.3587 } // Lahore default
        });

        @foreach($properties as $p)
            new google.maps.Marker({
                position: { lat: {{ $p->latitude ?? '31.5204' }}, lng: {{ $p->longitude ?? '74.3587' }} },
                map: map,
                title: "{{ $p->title }}"
            });
        @endforeach
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap" async defer></script>
@endpush
