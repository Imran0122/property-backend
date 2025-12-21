@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Edit Property</h2>

    <form action="{{ route('properties.update', $property->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Title --}}
        <div class="mb-3">
            <label class="form-label">Property Title</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $property->title) }}" required>
        </div>

        {{-- Location --}}
        <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control" value="{{ old('location', $property->location) }}" required>
        </div>

        {{-- Price --}}
        <div class="mb-3">
            <label class="form-label">Price (PKR)</label>
            <input type="number" name="price" class="form-control" value="{{ old('price', $property->price) }}" required>
        </div>

        {{-- Area --}}
        <div class="mb-3">
            <label for="area" class="form-label">Area (e.g. 10 Marla, 1200 Sq Ft)</label>
            <input type="text" name="area" id="area" class="form-control" value="{{ old('area', $property->area) }}">
        </div>

        {{-- City --}}
        <div class="mb-3">
            <label class="form-label">City</label>
            <select name="city_id" class="form-select" required>
                @foreach($cities as $city)
                    <option value="{{ $city->id }}" {{ $property->city_id == $city->id ? 'selected' : '' }}>
                        {{ $city->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Property Type --}}
        <div class="mb-3">
            <label class="form-label">Property Type</label>
            <select name="property_type_id" class="form-select" required>
                @foreach($types as $type)
                    <option value="{{ $type->id }}" {{ $property->property_type_id == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Description --}}
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description', $property->description) }}</textarea>
        </div>

        {{-- Amenities --}}
        <div class="mb-3">
            <label class="form-label">Amenities</label>
            <div class="row">
                @foreach($amenities as $amenity)
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" class="form-check-input"
                                   {{ $property->amenities->contains($amenity->id) ? 'checked' : '' }}>
                            <label class="form-check-label">{{ $amenity->name }}</label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Images --}}
        <div class="mb-3">
            <label class="form-label">Upload New Images (optional)</label>
            <input type="file" name="images[]" class="form-control" multiple>
        </div>

        {{-- Existing Images --}}
        <div class="mb-3">
            <label class="form-label">Current Images</label>
            <div class="d-flex flex-wrap">
                @foreach($property->images as $img)
                    <img src="{{ asset('storage/'.$img->path) }}" alt="img" class="img-thumbnail m-1" style="width:120px; height:90px; object-fit:cover;">
                @endforeach
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('properties.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
