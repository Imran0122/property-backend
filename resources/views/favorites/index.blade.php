@extends('layouts.app')

@section('content')
<div class="container py-6">
    <h1 class="text-2xl font-bold mb-4">My Favorite Properties</h1>

    @if($favorites->count())
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($favorites as $fav)
                <div class="bg-white shadow rounded overflow-hidden">
                    <img src="{{ $fav->property->images[0] ?? 'https://via.placeholder.com/400x200' }}" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h2 class="font-bold">{{ $fav->property->title }}</h2>
                        <p class="text-green-600 font-semibold">PKR {{ number_format($fav->property->price) }}</p>
                        <p class="text-gray-500">{{ $fav->property->location }}</p>
                        <a href="{{ route('properties.show', $fav->property->id) }}" class="btn btn-sm btn-success mt-2">View Details</a>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $favorites->links() }}</div>
    @else
        <p class="text-gray-600">You haven’t added any properties to favorites yet.</p>
    @endif
</div>
@endsection
