@foreach($properties as $property)
<div class="col-md-4">
    <div class="card h-100 shadow-sm">
        {{-- Property Image --}}
        @php
            $images = $property->images;

            // Agar JSON string hai to decode karo
            if (is_string($images)) {
                $images = json_decode($images, true);
            }
        @endphp

        @if(!empty($images) && isset($images[0]))
            <img src="{{ $images[0] }}" alt="{{ $property->title }}" class="card-img-top" style="height:200px; object-fit:cover;">
        @else
            <img src="https://via.placeholder.com/400x200" class="card-img-top" alt="No image">
        @endif

        {{-- Favorite Button --}}
        <button class="btn btn-outline-danger position-absolute top-0 end-0 m-2 favoriteBtn" data-id="{{ $property->id }}">
            <i class="bi @if(auth()->check() && $property->is_favorite) bi-heart-fill @else bi-heart @endif"></i>
        </button>

        <div class="card-body">
            <h5 class="card-title">{{ $property->title }}</h5>
            <p class="text-muted mb-1">{{ $property->city ?? '' }}</p>
            <p class="text-success fw-bold">PKR {{ number_format($property->price) }}</p>

            <ul class="list-inline mb-2">
                <li class="list-inline-item"><i class="bi bi-house-door-fill"></i> {{ ucfirst($property->type) }}</li>
                <li class="list-inline-item"><i class="bi bi-layout-text-sidebar-reverse"></i> {{ $property->area }} sq ft</li>
                <li class="list-inline-item"><i class="bi bi-door-open-fill"></i> {{ $property->bedrooms }} Beds</li>
                <li class="list-inline-item"><i class="bi bi-droplet-fill"></i> {{ $property->bathrooms }} Baths</li>
            </ul>

            <a href="{{ route('properties.show', $property->id) }}" class="btn btn-primary w-100">View Details</a>
        </div>
    </div>
</div>
@endforeach
