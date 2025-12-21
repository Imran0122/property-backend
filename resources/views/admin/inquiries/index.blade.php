@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Inquiries</h1>

    <table class="min-w-full bg-white border rounded">
        <thead>
            <tr>
                <th class="p-2 border">Property</th>
                <th class="p-2 border">Name</th>
                <th class="p-2 border">Email</th>
                <th class="p-2 border">Phone</th>
                <th class="p-2 border">Message</th>
                <th class="p-2 border">Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inquiries as $inquiry)
                <tr>
                    <td class="p-2 border">{{ $inquiry->property->title }}</td>
                    <td class="p-2 border">{{ $inquiry->name }}</td>
                    <td class="p-2 border">{{ $inquiry->email }}</td>
                    <td class="p-2 border">{{ $inquiry->phone }}</td>
                    <td class="p-2 border">{{ $inquiry->message }}</td>
                    <td class="p-2 border">{{ $inquiry->created_at->format('d M Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $inquiries->links() }}
    </div>
</div>
@endsection
