@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-6">
  <h1 class="text-2xl font-bold mb-6">Edit Property</h1>

  {{-- Property Basic Info --}}
  <div class="bg-white p-6 rounded shadow mb-6">
    <h2 class="text-lg font-semibold mb-4">Property Info</h2>
    <div><strong>Title:</strong> {{ $property->title }}</div>
    <div><strong>Price:</strong> ${{ number_format($property->price) }}</div>
    <div><strong>Status:</strong> {{ ucfirst($property->status) }}</div>
  </div>

  {{-- Image Gallery --}}
  <div class="bg-white p-6 rounded shadow mb-6">
    <h2 class="text-lg font-semibold mb-4">Property Images</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      @foreach($property->images as $img)
        <div class="relative border rounded overflow-hidden group">
          <img src="{{ asset('storage/'.$img->image_path) }}" class="w-full h-40 object-cover">

          @if($img->is_primary)
            <span class="absolute top-2 left-2 bg-green-600 text-white text-xs px-2 py-1 rounded">Primary</span>
          @endif

          <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 flex flex-col justify-center items-center gap-2">
            <button onclick="setPrimary({{ $property->id }}, {{ $img->id }})" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Set Primary</button>
            <button onclick="deleteImage({{ $img->id }})" class="bg-red-600 text-white px-3 py-1 rounded text-sm">Delete</button>
          </div>
        </div>
      @endforeach
    </div>
  </div>

  {{-- Add New Images --}}
  <div class="bg-white p-6 rounded shadow">
    <h2 class="text-lg font-semibold mb-4">Upload New Images</h2>
    <form action="{{ route('properties.update', $property) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <input type="file" name="images[]" multiple class="mb-4">
      <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Upload</button>
    </form>
  </div>
</div>

<script>
  async function setPrimary(propertyId, imageId) {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const res = await fetch(`/agent/properties/${propertyId}/set-primary-image`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token
      },
      body: JSON.stringify({ image_id: imageId })
    });
    const data = await res.json();
    if (data.success) {
      location.reload();
    } else {
      alert("Error setting primary image");
    }
  }

  async function deleteImage(imageId) {
    if (!confirm("Delete this image?")) return;
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const res = await fetch(`/agent/properties/image/${imageId}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': token
      }
    });
    const data = await res.json();
    if (data.success) {
      location.reload();
    } else {
      alert("Error deleting image");
    }
  }
</script>
@endsection
