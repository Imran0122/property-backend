@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">My Saved Properties</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($properties as $property)
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <a href="{{ route('properties.show', $property->id) }}">
                    <img src="{{ $property->images[0]->url ?? 'https://via.placeholder.com/400x200' }}" class="w-full h-48 object-cover" alt="Property">
                    <div class="p-4">
                        <h2 class="font-bold text-lg">{{ $property->title }}</h2>
                        <p class="text-green-600 font-semibold text-xl">PKR {{ number_format($property->price) }}</p>
                        <p class="text-gray-500">{{ $property->location }}</p>
                        <p class="text-sm text-gray-400">{{ $property->city->name ?? '' }}</p>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-span-3 text-center text-gray-500">You have no saved properties yet.</div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $properties->links() }}
    </div>
</div>
@endsection
