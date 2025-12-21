@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Reports Dashboard</h1>

    {{-- 🔹 Filters --}}
    <div class="bg-white p-4 rounded shadow mb-6">
        <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            
            {{-- Date Range --}}
            <div>
                <label class="text-sm text-gray-500">From Date</label>
                <input type="date" name="from" value="{{ request('from') }}" class="w-full border rounded px-2 py-1">
            </div>
            <div>
                <label class="text-sm text-gray-500">To Date</label>
                <input type="date" name="to" value="{{ request('to') }}" class="w-full border rounded px-2 py-1">
            </div>

            {{-- City --}}
            <div>
                <label class="text-sm text-gray-500">City</label>
                <select name="city" class="w-full border rounded px-2 py-1">
                    <option value="">All</option>
                    @foreach(App\Models\City::all() as $city)
                        <option value="{{ $city->id }}" {{ request('city') == $city->id ? 'selected' : '' }}>
                            {{ $city->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Property Type --}}
            <div>
                <label class="text-sm text-gray-500">Property Type</label>
                <select name="property_type" class="w-full border rounded px-2 py-1">
                    <option value="">All</option>
                    @foreach(App\Models\PropertyType::all() as $type)
                        <option value="{{ $type->id }}" {{ request('property_type') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-span-1 md:col-span-4 flex justify-between items-center mt-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded shadow">Apply Filters</button>

                {{-- Export Buttons --}}
                <div class="flex gap-2">
                    <a href="{{ route('reports.export.excel', request()->all()) }}" class="bg-green-600 text-white px-3 py-1 rounded">Export Excel</a>
                    <a href="{{ route('reports.export.pdf', request()->all()) }}" class="bg-red-600 text-white px-3 py-1 rounded">Export PDF</a>
                </div>
            </div>
        </form>
    </div>


    <div class="flex gap-2 mb-4">
    <a href="{{ route('reports.export.excel', request()->all()) }}" class="bg-green-600 text-white px-3 py-1 rounded">Export Excel</a>
    <a href="{{ route('reports.export.pdf', request()->all()) }}" class="bg-red-600 text-white px-3 py-1 rounded">Export PDF</a>
</div>


    {{-- 🔹 Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-4 rounded shadow text-center">
            <div class="text-sm text-gray-500">Total Properties</div>
            <div class="text-2xl font-bold">{{ $totalProperties ?? 0 }}</div>
        </div>
        <div class="bg-white p-4 rounded shadow text-center">
            <div class="text-sm text-gray-500">Total Leads</div>
            <div class="text-2xl font-bold">{{ $totalLeads ?? 0 }}</div>
        </div>
        <div class="bg-white p-4 rounded shadow text-center">
            <div class="text-sm text-gray-500">New Leads</div>
            <div class="text-2xl font-bold text-red-600">{{ $newLeads ?? 0 }}</div>
        </div>
        <div class="bg-white p-4 rounded shadow text-center">
            <div class="text-sm text-gray-500">Closed Leads</div>
            <div class="text-2xl font-bold text-green-600">{{ $closedLeads ?? 0 }}</div>
        </div>
    </div>

    {{-- 🔹 Properties by Type (Pie Chart) --}}
    <div class="bg-white p-6 rounded shadow mb-8">
        <h2 class="text-lg font-semibold mb-4">Properties by Type</h2>
        <canvas id="propertiesChart" height="120"></canvas>
    </div>

    {{-- 🔹 Leads per Month (Line Chart) --}}
    <div class="bg-white p-6 rounded shadow mb-12">
        <h2 class="text-lg font-semibold mb-4">Leads Per Month</h2>
        <canvas id="leadsChart" height="120"></canvas>
    </div>

    {{-- 🔹 Reported Properties List --}}
    <h2 class="text-xl font-bold mb-4">Reported Properties</h2>
    <div class="bg-white rounded shadow divide-y">
        @forelse($reports as $report)
            <div class="p-4 flex justify-between">
                <div>
                    <div class="font-semibold">
                        {{ $report->property->title }}
                        <span class="text-sm text-gray-500">({{ $report->property->location ?? 'N/A' }})</span>
                    </div>
                    <div class="text-sm text-gray-600 mt-1">
                        Reported by: {{ $report->user ? $report->user->name : 'Guest User' }}
                        <span class="text-xs text-gray-400">({{ $report->created_at->diffForHumans() }})</span>
                    </div>
                    <div class="text-sm text-red-600 mt-2">
                        Reason: {{ ucfirst($report->reason) }}
                    </div>
                    @if($report->message)
                        <div class="text-sm text-gray-500 mt-1">
                            "{{ $report->message }}"
                        </div>
                    @endif
                </div>

                <div class="text-right">
                    <form method="POST" action="{{ route('reports.update', $report) }}">
                        @csrf
                        @method('PATCH')
                        <select name="status" onchange="this.form.submit()" 
                            class="text-sm border rounded p-1">
                            <option value="pending" {{ $report->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="reviewed" {{ $report->status == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                            <option value="resolved" {{ $report->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </form>
                </div>
            </div>
        @empty
            <div class="p-6 text-gray-500">No reports found.</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $reports->links() }}</div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ✅ Properties Pie Chart
    const propertiesCtx = document.getElementById('propertiesChart');
    if(propertiesCtx){
        new Chart(propertiesCtx, {
            type: 'pie',
            data: {
                labels: @json($propertiesByType->pluck('propertyType.name') ?? []),
                datasets: [{
                    data: @json($propertiesByType->pluck('count') ?? []),
                    backgroundColor: ['#16a34a','#2563eb','#f59e0b','#dc2626','#9333ea']
                }]
            }
        });
    }

    // ✅ Leads Line Chart
    const leadsCtx = document.getElementById('leadsChart');
    if(leadsCtx){
        new Chart(leadsCtx, {
            type: 'line',
            data: {
                labels: @json($leadsPerMonth->keys()->map(fn($m) => date("F", mktime(0, 0, 0, $m, 1))) ?? []),
                datasets: [{
                    label: 'Leads',
                    data: @json($leadsPerMonth->values() ?? []),
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37,99,235,0.2)',
                    fill: true,
                    tension: 0.4
                }]
            }
        });
    }
</script>
@endpush
