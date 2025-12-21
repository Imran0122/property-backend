@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
  <div class="bg-white shadow rounded p-6">
    @if($agency->logo)
      <img src="{{ asset('storage/' . $agency->logo) }}" alt="{{ $agency->name }}" class="h-20 mb-4">
    @endif
    <h1 class="text-2xl font-bold">{{ $agency->name }}</h1>
    <p class="text-gray-600">{{ $agency->address }} | {{ $agency->phone }} | {{ $agency->email }}</p>
    <p class="mt-4">{{ $agency->description }}</p>
  </div>

  <h2 class="text-xl font-bold mt-8 mb-4">Agents in {{ $agency->name }}</h2>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @foreach($agency->agents as $agent)
      <a href="{{ route('agents.show', $agent) }}" class="block bg-white shadow rounded p-4">
        <div class="font-semibold">{{ $agent->user->name }}</div>
        <div class="text-sm text-gray-500">{{ $agent->phone }}</div>
      </a>
    @endforeach
  </div>

  <h2 class="text-xl font-bold mt-8 mb-4">Properties</h2>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @foreach($agency->properties as $property)
      <a href="{{ route('properties.show', $property) }}" class="block bg-white shadow rounded p-4">
        <div class="font-semibold">{{ $property->title }}</div>
        <div class="text-sm text-gray-500">{{ $property->location }}</div>
      </a>
    @endforeach
  </div>
</div>
@endsection
