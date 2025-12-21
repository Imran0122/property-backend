@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h3 class="mb-4 fw-bold">Transactions</h3>

    {{-- 🔹 Filters --}}
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-3">
            <input type="date" name="from" class="form-control" value="{{ request('from') }}" placeholder="From Date">
        </div>
        <div class="col-md-3">
            <input type="date" name="to" class="form-control" value="{{ request('to') }}" placeholder="To Date">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="succeeded" {{ request('status')=='succeeded'?'selected':'' }}>Succeeded</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                <option value="refunded" {{ request('status')=='refunded'?'selected':'' }}>Refunded</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="gateway" class="form-select">
                <option value="">All Gateways</option>
                <option value="stripe" {{ request('gateway')=='stripe'?'selected':'' }}>Stripe</option>
                <option value="paypal" {{ request('gateway')=='paypal'?'selected':'' }}>PayPal</option>
                <option value="bank" {{ request('gateway')=='bank'?'selected':'' }}>Bank</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    {{-- 🔹 Transactions Table --}}
    <div class="table-responsive bg-white shadow-sm rounded">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Property / Package</th>
                    <th>Gateway</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $t)
                    <tr>
                        <td>{{ $t->id }}</td>
                        <td>{{ $t->user->email ?? 'Guest' }}</td>
                        <td>
                            @if($t->property)
                                <a href="{{ route('admin.properties.show', $t->property) }}" class="text-decoration-none">
                                    {{ $t->property->title }}
                                </a>
                            @elseif($t->package)
                                {{ $t->package->name }}
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ strtoupper($t->gateway) }}</td>
                        <td>PKR {{ number_format($t->amount) }}</td>
                        <td>
                            @if($t->status == 'succeeded')
                                <span class="badge bg-success">Succeeded</span>
                            @elseif($t->status == 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($t->status == 'refunded')
                                <span class="badge bg-danger">Refunded</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($t->status) }}</span>
                            @endif
                        </td>
                        <td>{{ $t->created_at->format('d M Y, h:i A') }}</td>
                        <td>
                            <a href="{{ route('admin.transactions.show', $t) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No transactions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
