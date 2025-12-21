@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto p-6">
  <h1 class="text-2xl font-bold mb-6">Agent Wallets</h1>

  <table class="w-full bg-white border">
    <thead class="bg-gray-100">
      <tr>
        <th class="p-2">Agent</th>
        <th class="p-2">Balance</th>
        <th class="p-2">Last Updated</th>
        <th class="p-2">Action</th>
      </tr>
    </thead>
    <tbody>
      @foreach($wallets as $wallet)
      <tr>
        <td class="p-2 border">{{ $wallet->user->name }}</td>
        <td class="p-2 border text-green-700 font-semibold">${{ number_format($wallet->balance, 2) }}</td>
        <td class="p-2 border">{{ $wallet->updated_at->diffForHumans() }}</td>
        <td class="p-2 border">
          <form method="POST" action="{{ route('admin.wallets.addCredit', $wallet->id) }}">
            @csrf
            <input type="number" step="0.01" name="amount" class="border p-1 w-24 rounded" placeholder="Amount" required>
            <button class="bg-blue-600 text-white px-3 py-1 rounded">Add</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  <div class="mt-4">{{ $wallets->links() }}</div>
</div>
@endsection
