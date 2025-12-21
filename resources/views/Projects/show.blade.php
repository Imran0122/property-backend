@extends('layouts.app')

@section('title', $project->title . ' - ' . config('app.name'))

@section('content')
<div class="container py-4">
    {{-- 🖼 Cover Image --}}
    <div class="mb-4">
        @if($project->cover_image)
            <img src="{{ asset('storage/'.$project->cover_image) }}" class="img-fluid rounded shadow-sm" alt="{{ $project->title }}">
        @endif
    </div>

    {{-- 🏢 Project Info --}}
    <h1>{{ $project->title }}</h1>
    <p class="text-muted">{{ $project->location }} | {{ $project->city?->name }}</p>
    <span class="badge bg-{{ $project->status=='ongoing' ? 'warning':'success' }}">{{ ucfirst($project->status) }}</span>

    <h5 class="mt-3">Developer: <span class="fw-normal">{{ $project->developer ?? 'N/A' }}</span></h5>

    <div class="mt-4">
        {!! nl2br(e($project->description)) !!}
    </div>

    {{-- 🏠 Available Units --}}
    <div class="mt-5">
        <h3>Available Units</h3>
        @if($project->units->count())
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Bedrooms</th>
                    <th>Area (sqft)</th>
                    <th>Price</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($project->units as $unit)
                <tr>
                    <td>{{ $unit->title }}</td>
                    <td>{{ $unit->type }}</td>
                    <td>{{ $unit->bedrooms }}</td>
                    <td>{{ $unit->area }}</td>
                    <td>PKR {{ number_format($unit->price) }}</td>
                    <td><span class="badge bg-{{ $unit->status=='available'?'success':'secondary' }}">{{ ucfirst($unit->status) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
            <p>No units available.</p>
        @endif
    </div>

    {{-- 📍 Map Placeholder --}}
    <div class="mt-5">
        <h3>Location Map</h3>
        <div class="ratio ratio-16x9">
            <iframe src="https://maps.google.com/maps?q={{ urlencode($project->location) }}&output=embed" allowfullscreen></iframe>
        </div>
    </div>

    {{-- 🔗 Related Projects --}}
    @if($related->count())
    <div class="mt-5">
        <h3>Related Projects</h3>
        <div class="row">
            @foreach($related as $rp)
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <img src="{{ asset('storage/'.$rp->cover_image) }}" class="card-img-top" alt="{{ $rp->title }}">
                    <div class="card-body">
                        <h6><a href="{{ route('projects.show',$rp->slug) }}">{{ $rp->title }}</a></h6>
                        <p class="text-muted small mb-0">{{ $rp->location }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
