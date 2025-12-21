@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 fw-bold">Admin Dashboard</h2>

    {{-- ✅ Stats Cards --}}
    <div class="row text-center mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h4>{{ $stats['users'] }}</h4>
                <p class="text-muted">Total Users</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h4>{{ $stats['properties'] }}</h4>
                <p class="text-muted">Total Properties</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h4>{{ $stats['leads'] }}</h4>
                <p class="text-muted">Total Leads</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h4>{{ $stats['reports'] }}</h4>
                <p class="text-muted">Total Reports</p>
            </div>
        </div>
    </div>

    <div class="row text-center mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h4>{{ $stats['agencies'] }}</h4>
                <p class="text-muted">Total Agencies</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h4>{{ $stats['agents'] }}</h4>
                <p class="text-muted">Total Agents</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h4>{{ $stats['projects'] }}</h4>
                <p class="text-muted">Total Projects</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h4>{{ $stats['saved_searches'] }}</h4>
                <p class="text-muted">Saved Searches</p>
            </div>
        </div>
    </div>

    {{-- ✅ Properties Per Month Chart --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="fw-bold">Properties Added Per Month</h5>
            <canvas id="propertiesChart" height="100"></canvas>
        </div>
    </div>

    {{-- ✅ Leads Per Month Chart --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="fw-bold">Leads Per Month</h5>
            <canvas id="leadsChart" height="100"></canvas>
        </div>
    </div>

    {{-- ✅ Latest Properties --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Latest Properties</h5>
            <table class="table">
                <thead><tr><th>Title</th><th>Price</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                    @foreach($latestProperties as $p)
                        <tr>
                            <td>{{ $p->title }}</td>
                            <td>PKR {{ number_format($p->price) }}</td>
                            <td>{{ ucfirst($p->status) }}</td>
                            <td>{{ $p->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ✅ Latest Leads --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Latest Leads</h5>
            <table class="table">
                <thead><tr><th>Name</th><th>Email</th><th>Property</th><th>Date</th></tr></thead>
                <tbody>
                    @foreach($latestLeads as $lead)
                        <tr>
                            <td>{{ $lead->name }}</td>
                            <td>{{ $lead->email }}</td>
                            <td>{{ $lead->property->title ?? 'N/A' }}</td>
                            <td>{{ $lead->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ✅ Latest Reports --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Latest Reports</h5>
            <table class="table">
                <thead><tr><th>Reason</th><th>Message</th><th>Property</th><th>Date</th></tr></thead>
                <tbody>
                    @foreach($latestReports as $report)
                        <tr>
                            <td>{{ ucfirst($report->reason) }}</td>
                            <td>{{ $report->message }}</td>
                            <td>{{ $report->property->title ?? 'N/A' }}</td>
                            <td>{{ $report->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ✅ Latest Agencies --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Latest Agencies</h5>
            <table class="table">
                <thead><tr><th>Name</th><th>City</th><th>Created At</th></tr></thead>
                <tbody>
                    @foreach($latestAgencies as $agency)
                        <tr>
                            <td>{{ $agency->name }}</td>
                            <td>{{ $agency->city }}</td>
                            <td>{{ $agency->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ✅ Latest Agents --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Latest Agents</h5>
            <table class="table">
                <thead><tr><th>Name</th><th>Email</th><th>Agency</th><th>Created At</th></tr></thead>
                <tbody>
                    @foreach($latestAgents as $agent)
                        <tr>
                            <td>{{ $agent->name }}</td>
                            <td>{{ $agent->email }}</td>
                            <td>{{ $agent->agency->name ?? 'N/A' }}</td>
                            <td>{{ $agent->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ✅ Latest Projects --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Latest Projects</h5>
            <table class="table">
                <thead><tr><th>Name</th><th>Location</th><th>Status</th><th>Created At</th></tr></thead>
                <tbody>
                    @foreach($latestProjects as $project)
                        <tr>
                            <td>{{ $project->name }}</td>
                            <td>{{ $project->location }}</td>
                            <td>{{ ucfirst($project->status) }}</td>
                            <td>{{ $project->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const propertiesCtx = document.getElementById('propertiesChart').getContext('2d');
    new Chart(propertiesCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($propertiesPerMonth->toArray())) !!},
            datasets: [{
                label: 'Properties',
                data: {!! json_encode(array_values($propertiesPerMonth->toArray())) !!},
                borderColor: 'rgba(75, 192, 192, 1)',
                tension: 0.4
            }]
        }
    });

    const leadsCtx = document.getElementById('leadsChart').getContext('2d');
    new Chart(leadsCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($leadsPerMonth->toArray())) !!},
            datasets: [{
                label: 'Leads',
                data: {!! json_encode(array_values($leadsPerMonth->toArray())) !!},
                backgroundColor: 'rgba(153, 102, 255, 0.7)'
            }]
        }
    });
</script>
@endpush
