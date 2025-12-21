@extends('layouts.app')

@section('title', 'New Projects - ' . config('app.name'))

@section('content')
<div class="container py-4">
    <h1 class="mb-4">New Projects</h1>

    {{-- 🔍 Filters (city, status, search) --}}
    <form method="GET" action="{{ route('projects.index') }}" class="mb-4 row g-2">
        <div class="col-md-3">
            <input type="text" name="q" class="form-control" placeholder="Search projects..." value="{{ request('q') }}">
        </div>
        <div class="col-md-3">
            <select name="city_id" class="form-select">
                <option value="">All Cities</option>
                @foreach(\App\Models\City::all() as $city)
                    <option value="{{ $city->id }}" {{ request('city_id')==$city->id ? 'selected':'' }}>{{ $city->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Status</option>
                <option value="ongoing" {{ request('status')=='ongoing' ? 'selected':'' }}>Ongoing</option>
                <option value="completed" {{ request('status')=='completed' ? 'selected':'' }}>Completed</option>
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    {{-- 📋 Projects Grid --}}
    <div class="row">
        @foreach($projects as $project)
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                @if($project->cover_image)
                    <img src="{{ asset('storage/'.$project->cover_image) }}" class="card-img-top" alt="{{ $project->title }}">
                @else
                    <img src="{{ asset('images/default-project.jpg') }}" class="card-img-top" alt="{{ $project->title }}">
                @endif

                <div class="card-body">
                    <h5 class="card-title">
                        <a href="{{ route('projects.show',$project->slug) }}" class="text-decoration-none">{{ $project->title }}</a>
                    </h5>
                    <p class="text-muted mb-1">{{ $project->location }}</p>
                    <span class="badge bg-{{ $project->status=='ongoing' ? 'warning':'success' }}">
                        {{ ucfirst($project->status) }}
                    </span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- 📄 Pagination --}}
    <div class="mt-4">
        {{ $projects->links() }}
    </div>
</div>
@endsection
