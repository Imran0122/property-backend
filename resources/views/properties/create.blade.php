@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Add New Property</h2>

    <form action="{{ route('properties.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Title --}}
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" 
                   class="form-control" placeholder="Enter property title" value="{{ old('title') }}" required>
        </div>

        {{-- Location --}}
        <div class="mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" name="location" id="location" 
                   class="form-control" placeholder="Enter location" value="{{ old('location') }}" required>
        </div>

        {{-- Price --}}
        <div class="mb-3">
            <label for="price" class="form-label">Price (PKR)</label>
            <input type="number" step="0.01" name="price" id="price" 
                   class="form-control" placeholder="Enter price" value="{{ old('price') }}" required>
        </div>

        {{-- Area --}}
        <div class="mb-3">
            <label for="area" class="form-label">Area (e.g. 10 Marla, 1200 Sq Ft)</label>
            <input type="text" name="area" id="area" 
                   class="form-control" placeholder="Enter property area" value="{{ old('area') }}">
        </div>

        {{-- City Dropdown --}}
        <div class="mb-3">
            <label for="city_id" class="form-label">City</label>
            <select name="city_id" id="city_id" class="form-select" required>
                <option value="">-- Select City --</option>
                @foreach($cities as $city)
                    <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                        {{ $city->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Property Type --}}
        <div class="mb-3">
            <label for="property_type_id" class="form-label">Property Type</label>
            <select name="property_type_id" id="property_type_id" class="form-select" required>
                <option value="">-- Select Type --</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}" {{ old('property_type_id') == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Description --}}
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" 
                      class="form-control" rows="4" 
                      placeholder="Enter property description">{{ old('description') }}</textarea>
        </div>

        {{-- Amenities --}}
        <div class="mb-3">
            <label class="form-label">Amenities</label>
            <div class="row">
                @foreach($amenities as $amenity)
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" 
                                   class="form-check-input" {{ (is_array(old('amenities')) && in_array($amenity->id, old('amenities'))) ? 'checked' : '' }}>
                            <label class="form-check-label">{{ $amenity->name }}</label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Images --}}
        <div class="mb-3">
            <label for="images" class="form-label">Property Images</label>
            <input type="file" name="images[]" id="images" class="form-control" multiple>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn btn-primary">Save Property</button>
        <input type="file" name="images[]" multiple class="border rounded p-2">

    </form>
</div>
@endsection
