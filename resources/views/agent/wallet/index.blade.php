@extends('layouts.agent')

@section('content')
<div class="max-w-6xl mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4">My Wallet</h1>

  <div class="bg-white shadow rounded p-4 mb-4">
    <div class="text-gray-700">Current Balance:</div>
    <div class="text-3xl font-bold text-green-600">${{ number_format($wallet->balance, 2) }}</div>
  </div>

  <h2 class="text-xl font-semibold mb-2">Recent Transactions</h2>
  <table class="w-full bg-white border rounded shadow">
    <thead class="bg-gray-100">
      <tr>
        <th class="p-2">Date</th>
        <th class="p-2">Description</th>
        <th class="p-2">Amount</th>
        <th class="p-2">Type</th>
      </tr>
    </thead>
    <tbody>
      @foreach($transactions as $txn)
      <tr>
        <td class="p-2 border">{{ $txn->created_at->format('d M Y') }}</td>
        <td class="p-2 border">{{ $txn->description }}</td>
        <td class="p-2 border">${{ number_format($txn->amount, 2) }}</td>
        <td class="p-2 border {{ $txn->type == 'credit' ? 'text-green-600' : 'text-red-600' }}">{{ ucfirst($txn->type) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  <div class="mt-4">{{ $transactions->links() }}</div>
</div>
@endsection
