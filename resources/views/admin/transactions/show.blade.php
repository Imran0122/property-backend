@extends('layouts.admin')
@section('content')
<div class="container py-4">
  <h3>Transaction #{{ $transaction->id }}</h3>
  <p>User: {{ $transaction->user->email ?? '—' }}</p>
  <p>Amount: {{ $transaction->currency }} {{ number_format($transaction->amount,2) }}</p>
  <p>Gateway: {{ $transaction->gateway }}</p>
  <p>Status: <span class="badge bg-{{ $transaction->status=='succeeded' ? 'success':'secondary' }}">{{ $transaction->status }}</span></p>

  <form method="POST" action="{{ route('admin.transactions.refund', $transaction) }}">
    @csrf
    <div class="mb-2">
      <label>Refund amount (optional)</label>
      <input type="text" name="amount" class="form-control" value="{{ $transaction->amount }}">
    </div>
    <button class="btn btn-danger" onclick="return confirm('Proceed with refund?')">Process Refund</button>
  </form>

  <hr>
  <h5>Payload</h5>
  <pre style="white-space:pre-wrap;">{{ json_encode($transaction->payload, JSON_PRETTY_PRINT) }}</pre>
</div>
@endsection
