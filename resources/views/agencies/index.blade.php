@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4">Real Estate Agencies</h1>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @foreach($agencies as $agency)
      <a href="{{ route('agencies.show', $agency) }}" class="block bg-white rounded shadow p-4 hover:shadow-lg">
        @if($agency->logo)
          <img src="{{ asset('storage/' . $agency->logo) }}" alt="{{ $agency->name }}" class="h-16 mb-3">
        @endif
        <h2 class="font-semibold text-lg">{{ $agency->name }}</h2>
        <p class="text-sm text-gray-500">{{ Str::limit($agency->description, 80) }}</p>
        <div class="text-xs text-gray-400 mt-2">
          {{ $agency->agents_count }} Agents • {{ $agency->properties_count }} Properties
        </div>
      </a>
    @endforeach
  </div>

  <div class="mt-6">{{ $agencies->links() }}</div>
</div>
@endsection
