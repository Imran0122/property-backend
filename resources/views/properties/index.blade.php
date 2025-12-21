@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    <!-- Title -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Properties for Sale</h1>
        <p class="text-gray-600">{{ $properties->total() }} listings found</p>
    </div>

    <!-- 🔍 Filters -->
    <form method="GET" action="{{ route('properties.index') }}" 
          class="bg-white shadow p-4 rounded-lg mb-6 grid grid-cols-1 md:grid-cols-7 gap-4">

        <!-- City -->
        <select name="city_id" class="border rounded p-2">
            <option value="">All Cities</option>
            @foreach($cities as $city)
                <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                    {{ $city->name }}
                </option>
            @endforeach
        </select>

        <!-- Property Type -->
        <select name="property_type_id" class="border rounded p-2">
            <option value="">All Types</option>
            @foreach($types as $type)
                <option value="{{ $type->id }}" {{ request('property_type_id') == $type->id ? 'selected' : '' }}>
                    {{ $type->name }}
                </option>
            @endforeach
        </select>

        <!-- Price Range -->
        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min Price" class="border rounded p-2">
        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max Price" class="border rounded p-2">

        <!-- Bedrooms -->
        <select name="bedrooms" class="border rounded p-2">
            <option value="">Bedrooms</option>
            <option value="1" {{ request('bedrooms') == 1 ? 'selected' : '' }}>1+</option>
            <option value="2" {{ request('bedrooms') == 2 ? 'selected' : '' }}>2+</option>
            <option value="3" {{ request('bedrooms') == 3 ? 'selected' : '' }}>3+</option>
            <option value="4" {{ request('bedrooms') == 4 ? 'selected' : '' }}>4+</option>
        </select>

        <!-- Status -->
        <select name="status" class="border rounded p-2">
            <option value="">Any Status</option>
            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
            <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Sold</option>
            <option value="rented" {{ request('status') == 'rented' ? 'selected' : '' }}>Rented</option>
        </select>

        <!-- Keyword -->
        <input type="text" name="keyword" value="{{ request('keyword') }}" 
               placeholder="Keyword (e.g. DHA, corner)" class="border rounded p-2">

        <!-- Search Btn -->
        <button class="bg-green-600 text-white px-4 py-2 rounded col-span-full md:col-span-1">
            Search
        </button>
    </form>

    <!-- 📋 Listings -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($properties as $property)
            <div class="bg-white shadow rounded-lg overflow-hidden hover:shadow-lg transition">
                <a href="{{ route('properties.show', $property->id) }}">
                    <img src="{{ $property->images->first()?->image_path 
                        ? asset('storage/'.$property->images->first()->image_path) 
                        : 'https://via.placeholder.com/400x200' }}" 
                        class="w-full h-48 object-cover" alt="Property">

                    <div class="p-4">
                        <h2 class="font-bold text-lg truncate">{{ $property->title }}</h2>
                        <p class="text-green-600 font-semibold text-xl">
                            PKR {{ number_format($property->price) }}
                        </p>
                        <p class="text-gray-500">{{ $property->location }}</p>
                        <p class="text-sm text-gray-400">
                            {{ $property->city->name ?? '' }}
                        </p>

                        <div class="flex justify-between text-sm text-gray-500 mt-2">
                            <span>{{ $property->bedrooms }} 🛏</span>
                            <span>{{ $property->bathrooms }} 🛁</span>
                            <span>{{ $property->area ?? '—' }} sqft</span>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-span-3 text-center text-gray-500">No properties found.</div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $properties->withQueryString()->links() }}
    </div>
</div>
@endsection
