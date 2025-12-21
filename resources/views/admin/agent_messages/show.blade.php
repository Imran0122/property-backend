@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4">Message Details</h2>

    {{-- Message Card --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">{{ $message->name }}</h5>
            <p class="mb-1"><strong>Email:</strong> {{ $message->email }}</p>
            <p class="mb-1"><strong>Phone:</strong> {{ $message->phone }}</p>
            <p class="mb-1">
                <strong>Property:</strong>
                @if($message->property)
                    <a href="{{ route('properties.show', $message->property->id) }}" target="_blank">
                        {{ $message->property->title }}
                    </a>
                @else
                    N/A
                @endif
            </p>
            <hr>
            <p><strong>Message:</strong></p>
            <p class="border rounded p-3 bg-light">{{ $message->message }}</p>
            <p class="text-muted small mt-3">
                Sent on {{ $message->created_at->format('d M Y h:i A') }}
            </p>
        </div>
    </div>

    {{-- Reply Form --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white">
            <strong>Reply to {{ $message->name }}</strong>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.agent-messages.reply', $message->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <textarea name="reply_message" class="form-control" rows="4" placeholder="Write your reply..." required></textarea>
                </div>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-send"></i> Send Reply
                </button>
            </form>
        </div>
    </div>

    {{-- Reply History --}}
    @if($message->replies->count() > 0)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <strong>Reply History</strong>
            </div>
            <div class="card-body">
                @foreach($message->replies as $reply)
                    <div class="border-start border-3 border-success ps-3 mb-3">
                        <p class="mb-1">{{ $reply->reply_message }}</p>
                        <small class="text-muted">Sent on {{ $reply->created_at->format('d M Y h:i A') }}</small>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Actions --}}
    <div class="mt-3">
        <a href="{{ route('admin.agent-messages.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Messages
        </a>
        <form action="{{ route('admin.agent-messages.destroy', $message->id) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this message?')">
                <i class="bi bi-trash"></i> Delete
            </button>
        </form>
    </div>
</div>
@endsection
