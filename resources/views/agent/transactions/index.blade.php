@extends('layouts.agent')

@section('content')
<div class="max-w-6xl mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4">My Transactions</h1>

  <table class="w-full bg-white border rounded shadow">
    <thead class="bg-gray-100">
      <tr>
        <th class="p-2 text-left">Date</th>
        <th class="p-2 text-left">Property</th>
        <th class="p-2 text-left">Amount</th>
        <th class="p-2 text-left">Status</th>
      </tr>
    </thead>
    <tbody>
      @foreach($transactions as $txn)
      <tr>
        <td class="p-2 border">{{ $txn->created_at->format('d M Y') }}</td>
        <td class="p-2 border">{{ $txn->property->title ?? 'N/A' }}</td>
        <td class="p-2 border">${{ number_format($txn->amount, 2) }}</td>
        <td class="p-2 border">
          <span class="px-2 py-1 text-xs rounded {{ $txn->status == 'paid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
            {{ ucfirst($txn->status) }}
          </span>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <div class="mt-4">{{ $transactions->links() }}</div>
</div>
@endsection
