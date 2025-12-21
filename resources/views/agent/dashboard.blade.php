@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">

  <h1 class="text-2xl font-bold mb-6">Agent Dashboard</h1>

  <!-- Stats Cards -->
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-4 rounded shadow text-center">
      <div class="text-sm text-gray-500">Total Properties</div>
      <div class="text-2xl font-bold text-green-600">{{ $totalProperties }}</div>
    </div>
    <div class="bg-white p-4 rounded shadow text-center">
      <div class="text-sm text-gray-500">Active</div>
      <div class="text-2xl font-bold text-blue-600">{{ $activeProperties }}</div>
    </div>
    <div class="bg-white p-4 rounded shadow text-center">
      <div class="text-sm text-gray-500">Pending</div>
      <div class="text-2xl font-bold text-yellow-600">{{ $pendingProperties }}</div>
    </div>
    <div class="bg-white p-4 rounded shadow text-center">
      <div class="text-sm text-gray-500">New Leads</div>
      <div class="text-2xl font-bold text-red-600">{{ $newLeads }}</div>
    </div>
  </div>

  <!-- Action Buttons -->
  <div class="flex gap-4 mb-10">
    <a href="{{ route('agent.properties.index') }}" class="bg-green-600 text-white px-4 py-2 rounded">Manage Properties</a>
    <a href="{{ route('agent.leads.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded">View Leads</a>
  </div>

  <!-- Charts -->
  <div class="grid md:grid-cols-2 gap-6 mb-10">
    
    <!-- Leads by Month -->
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="font-bold mb-4">Leads by Month</h3>
      <canvas id="leadsChart" height="200"></canvas>
    </div>

    <!-- Properties Overview -->
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="font-bold mb-4">Properties Overview</h3>
      <canvas id="propertiesChart" height="200"></canvas>
    </div>

  </div>

  <!-- Latest Properties -->
  <div class="bg-white shadow rounded-lg p-6 mb-10">
    <h3 class="font-bold mb-4">Latest Properties</h3>
    <table class="w-full text-left border">
      <thead class="bg-gray-100">
        <tr>
          <th class="p-2">Title</th>
          <th class="p-2">Price</th>
          <th class="p-2">Status</th>
          <th class="p-2">Date</th>
        </tr>
      </thead>
      <tbody>
        @foreach($latestProperties as $p)
          <tr class="border-t">
            <td class="p-2">{{ $p->title }}</td>
            <td class="p-2">PKR {{ number_format($p->price) }}</td>
            <td class="p-2">{{ ucfirst($p->status) }}</td>
            <td class="p-2">{{ $p->created_at->diffForHumans() }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <!-- Latest Leads -->
  <div class="bg-white shadow rounded-lg p-6 mb-10">
    <h3 class="font-bold mb-4">Latest Leads</h3>
    <table class="w-full text-left border">
      <thead class="bg-gray-100">
        <tr>
          <th class="p-2">Name</th>
          <th class="p-2">Email</th>
          <th class="p-2">Property</th>
          <th class="p-2">Date</th>
        </tr>
      </thead>
      <tbody>
        @foreach($latestLeads as $lead)
          <tr class="border-t">
            <td class="p-2">{{ $lead->name }}</td>
            <td class="p-2">{{ $lead->email }}</td>
            <td class="p-2">{{ $lead->property->title ?? 'N/A' }}</td>
            <td class="p-2">{{ $lead->created_at->diffForHumans() }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <!-- Latest Messages -->
  <div class="bg-white shadow rounded-lg p-6 mb-10">
    <h3 class="font-bold mb-4">Latest Messages</h3>
    <table class="w-full text-left border">
      <thead class="bg-gray-100">
        <tr>
          <th class="p-2">From</th>
          <th class="p-2">Message</th>
          <th class="p-2">Date</th>
        </tr>
      </thead>
      <tbody>
        @foreach($latestMessages as $msg)
          <tr class="border-t">
            <td class="p-2">{{ $msg->sender->name ?? 'N/A' }}</td>
            <td class="p-2">{{ Str::limit($msg->body, 40) }}</td>
            <td class="p-2">{{ $msg->created_at->diffForHumans() }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Leads by Month
  const leadsCtx = document.getElementById('leadsChart');
  new Chart(leadsCtx, {
    type: 'bar',
    data: {
      labels: @json(array_keys($monthlyLeads->toArray())),
      datasets: [{
        label: 'Leads',
        data: @json(array_values($monthlyLeads->toArray())),
        backgroundColor: '#10B981'
      }]
    }
  });

  // Properties Overview
  const propCtx = document.getElementById('propertiesChart');
  new Chart(propCtx, {
    type: 'pie',
    data: {
      labels: ['Active', 'Pending', 'Inactive'],
      datasets: [{
        data: [
          {{ $activeProperties }},
          {{ $pendingProperties }},
          {{ $totalProperties - ($activeProperties + $pendingProperties) }}
        ],
        backgroundColor: ['#3B82F6', '#FACC15', '#EF4444']
      }]
    }
  });
</script>
@endpush
