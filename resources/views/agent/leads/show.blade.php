@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4">Lead Details</h1>

  <div class="bg-white rounded shadow p-6 space-y-4">
    <div>
      <h2 class="text-lg font-semibold">{{ $lead->name }}</h2>
      <p class="text-gray-600 text-sm">{{ $lead->email }} | {{ $lead->phone }}</p>
    </div>

    <div>
      <h3 class="text-sm font-semibold text-gray-500">Message</h3>
      <p class="text-gray-800">{{ $lead->message ?? 'No message provided' }}</p>
    </div>

    <div>
      <h3 class="text-sm font-semibold text-gray-500">Property</h3>
      <a href="{{ route('properties.show', $lead->property_id) }}" class="text-blue-600 hover:underline">
        {{ $lead->property->title }}
      </a>
    </div>

    <div class="flex items-center space-x-3">
      <span class="text-sm font-medium">Status:</span>
      <form action="{{ route('agent.leads.update', $lead) }}" method="POST">
        @csrf
        @method('PATCH')
        <select name="status" onchange="this.form.submit()" class="border rounded p-1 text-sm">
          <option value="new" {{ $lead->status == 'new' ? 'selected' : '' }}>New</option>
          <option value="contacted" {{ $lead->status == 'contacted' ? 'selected' : '' }}>Contacted</option>
          <option value="converted" {{ $lead->status == 'converted' ? 'selected' : '' }}>Converted</option>
          <option value="not_interested" {{ $lead->status == 'not_interested' ? 'selected' : '' }}>Not Interested</option>
        </select>
      </form>
    </div>

    <div class="text-xs text-gray-400">Received {{ $lead->created_at->format('d M Y h:i A') }}</div>
  </div>
</div>
@endsection
