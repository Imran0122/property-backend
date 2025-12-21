
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4">Agent Messages</h2>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Property</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($messages as $msg)
                            <tr>
                                <td>{{ $msg->id }}</td>
                                <td>
                                    @if($msg->property)
                                        <a href="{{ route('properties.show', $msg->property->id) }}" target="_blank">
                                            {{ $msg->property->title }}
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $msg->name }}</td>
                                <td>{{ $msg->email }}</td>
                                <td>{{ $msg->phone }}</td>
                                <td>{{ Str::limit($msg->message, 50) }}</td>
                                <td>
                                    @if($msg->status == 'unread')
                                        <span class="badge bg-warning text-dark">Unread</span>
                                    @elseif($msg->status == 'read')
                                        <span class="badge bg-secondary">Read</span>
                                    @else
                                        <span class="badge bg-success">Replied</span>
                                    @endif
                                </td>
                                <td>{{ $msg->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.agent-messages.show', $msg->id) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.agent-messages.destroy', $msg->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this message?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-3">
                                    No messages found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $messages->links() }}
    </div>
</div>
@endsection
