@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4">Leads / Inquiries</h1>

  <div class="bg-white rounded shadow divide-y">
    @forelse($leads as $lead)
      <a href="{{ route('agent.leads.show', $lead) }}" class="block p-4 hover:bg-gray-50 flex justify-between">
        <div>
          <div class="font-semibold">
            {{ $lead->name }} 
            @if($lead->user) 
              <span class="text-sm text-gray-400">({{ $lead->user->email }})</span> 
            @endif
          </div>
          <div class="text-sm text-gray-500">{{ Str::limit($lead->message, 80) }}</div>
          <div class="text-xs text-gray-400">Property: {{ $lead->property->title }}</div>
        </div>
        <div class="text-right">
          <div class="text-sm">{{ $lead->created_at->diffForHumans() }}</div>
          <div class="mt-2">
            <span class="px-2 py-1 text-xs rounded 
              @if($lead->status == 'new') bg-red-100 text-red-700
              @elseif($lead->status == 'contacted') bg-yellow-100 text-yellow-700
              @elseif($lead->status == 'converted') bg-green-100 text-green-700
              @else bg-gray-100 text-gray-700 @endif">
              {{ ucfirst($lead->status) }}
            </span>
          </div>
        </div>
      </a>
    @empty
      <div class="p-4 text-center text-gray-500">No inquiries yet.</div>
    @endforelse
  </div>

  <div class="mt-4">{{ $leads->links() }}</div>
</div>
@endsection
