@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4">Our Agents</h1>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @foreach($agents as $agent)
      <a href="{{ route('agents.show', $agent) }}" class="block bg-white shadow rounded p-4 hover:shadow-lg transition">
        <div class="font-semibold text-lg">{{ $agent->user->name }}</div>
        <div class="text-sm text-gray-500">{{ $agent->company }}</div>
        <div class="text-sm mt-2">{{ Str::limit($agent->bio, 80) }}</div>
      </a>
    @endforeach
  </div>

  <div class="mt-6">{{ $agents->links() }}</div>
</div>
@endsection
