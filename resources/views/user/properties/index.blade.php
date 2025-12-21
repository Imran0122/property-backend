@extends('layouts.app')

@section('content')
<div class="container">
    <h1>My Properties</h1>
    <a href="#" class="btn btn-primary mb-3">Add New Property</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>City</th>
                <th>Type</th>
                <th>Price</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($properties as $property)
            <tr>
                <td>{{ $property->title }}</td>
                <td>{{ $property->city->name }}</td>
                <td>{{ $property->propertyType->name }}</td>
                <td>{{ $property->price }}</td>
                <td>{{ $property->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $properties->links() }}
</div>
@endsection
