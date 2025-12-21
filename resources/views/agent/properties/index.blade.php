@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4">My Properties</h1>

  <div class="grid gap-4">
    @foreach($properties as $prop)
      <div class="bg-white p-4 rounded shadow flex gap-4 items-center relative">

        {{-- ⭐ Featured Badge --}}
        @if($prop->is_featured && $prop->featured_until && $prop->featured_until->isFuture())
          <span class="absolute top-2 left-2 bg-yellow-400 text-white text-xs font-bold px-2 py-1 rounded">FEATURED</span>
        @endif

        {{-- 🏠 Property Image --}}
        <div class="w-36 h-24 bg-gray-100 overflow-hidden rounded">
          @php $img = $prop->images->firstWhere('is_primary', 1) ?? $prop->images->first(); @endphp
          @if($img)
            <img src="{{ asset('storage/'.$img->image_path) }}" class="w-full h-full object-cover" alt="">
          @else
            <div class="w-full h-full flex items-center justify-center text-gray-400">No Image</div>
          @endif
        </div>

        {{-- 📋 Property Details --}}
        <div class="flex-1">
          <a href="{{ route('properties.show', $prop) }}" class="font-semibold text-lg">{{ $prop->title }}</a>
          <div class="text-sm text-gray-500">{{ $prop->city ?? $prop->location }}</div>
          <div class="text-green-600 font-bold">${{ number_format($prop->price) }}</div>

          {{-- ⏰ Featured expiry info --}}
          @if($prop->is_featured && $prop->featured_until)
            <div class="text-xs text-gray-400">Featured until: {{ $prop->featured_until->format('M d, Y') }}</div>
          @endif
        </div>

        {{-- ⚙️ Actions --}}
        <div class="flex flex-col gap-2 items-end">
          <a href="{{ route('agent.properties.edit', $prop) }}" class="px-3 py-1 bg-yellow-500 text-white rounded">Edit</a>

          <select onchange="changeStatus({{ $prop->id }}, this.value)" class="border p-1 rounded text-sm">
            <option value="pending" {{ $prop->status=='pending' ? 'selected' : '' }}>Pending</option>
            <option value="active" {{ $prop->status=='active' ? 'selected' : '' }}>Active</option>
            <option value="sold" {{ $prop->status=='sold' ? 'selected' : '' }}>Sold</option>
            <option value="rented" {{ $prop->status=='rented' ? 'selected' : '' }}>Rented</option>
          </select>

          <a href="{{ route('agent.properties.edit', $prop) }}" class="text-sm text-gray-500 underline">Manage Images</a>

          {{-- ⭐ Feature/Unfeature Button (only for admin/agent) --}}
          @if(Auth::user() && Auth::user()->is_admin)
            @if(!$prop->is_featured)
              <button onclick="toggleFeatured({{ $prop->id }}, true)" class="px-3 py-1 bg-green-500 text-white text-xs rounded">Make Featured</button>
            @else
              <button onclick="toggleFeatured({{ $prop->id }}, false)" class="px-3 py-1 bg-gray-400 text-white text-xs rounded">Unfeature</button>
            @endif
          @endif
        </div>
      </div>
    @endforeach

    <div>{{ $properties->links() }}</div>
  </div>
</div>

<script>
  async function changeStatus(propertyId, status) {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const res = await fetch(`/agent/properties/${propertyId}/status`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token
      },
      body: JSON.stringify({ status })
    });
    const data = await res.json();
    if (data.success) {
      alert('Status updated: ' + data.status);
    } else {
      alert('Error updating status');
    }
  }

  async function toggleFeatured(propertyId, makeFeatured) {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const res = await fetch(`/admin/properties/${propertyId}/feature`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token
      },
      body: JSON.stringify({ make_featured: makeFeatured })
    });
    const data = await res.json();
    if (data.success) {
      alert(data.message);
      location.reload();
    } else {
      alert('Error updating featured status');
    }
  }
</script>
@endsection
