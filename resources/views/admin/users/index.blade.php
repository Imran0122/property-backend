@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h3 class="fw-bold mb-3">Users Management</h3>
    <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">+ Add New User</a>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $u)
            <tr>
                <td>{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td><span class="badge bg-info">{{ ucfirst($u->role) }}</span></td>
                <td>
                    <span class="badge {{ $u->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                        {{ ucfirst($u->status) }}
                    </span>
                </td>
                <td>{{ $u->created_at->diffForHumans() }}</td>
                <td>
                    <a href="{{ route('users.edit', $u) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('users.destroy', $u) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete user?')">Delete</button>
                    </form>
                    <form action="{{ route('users.toggleStatus', $u) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-secondary">{{ $u->status == 'active' ? 'Block' : 'Unblock' }}</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $users->links() }}
</div>
@endsection
