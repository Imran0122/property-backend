@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-6">
  <div class="bg-white shadow rounded p-6">
    <h1 class="text-2xl font-bold">{{ $agent->user->name }}</h1>
    <p class="text-gray-600">{{ $agent->company }} | {{ $agent->phone }}</p>
    <p class="mt-4">{{ $agent->bio }}</p>
  </div>

  <h2 class="text-xl font-bold mt-8 mb-4">Properties by {{ $agent->user->name }}</h2>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @foreach($agent->properties as $property)
      <a href="{{ route('properties.show', $property) }}" class="block bg-white shadow rounded p-4">
        <div class="font-semibold">{{ $property->title }}</div>
        <div class="text-sm text-gray-500">{{ $property->location }}</div>
      </a>
    @endforeach
  </div>
</div>
@endsection
