@extends('layouts.app')

@section('content')
<div class="row">
  <aside class="col-lg-3">
    <div class="list-group">
      <a class="list-group-item list-group-item-action active">Dashboard</a>
      <a class="list-group-item list-group-item-action" href="{{ route('properties.create') }}">Add Property</a>
      <a class="list-group-item list-group-item-action" href="{{ route('dashboard') }}">My Properties</a>
    </div>
  </aside>
  <div class="col-lg-9">
    <h4>Welcome, {{ auth()->user()->name }}</h4>
    <p>Your properties</p>
    {{-- Show user's properties --}}
    <div class="row g-3">
      @foreach(auth()->user()->properties ?? [] as $p)
        <div class="col-md-6">
          <div class="card">
            <img src="{{ asset('images/'.($p->images[0] ?? '')) }}" class="card-img-top" style="height:150px;object-fit:cover;">
            <div class="card-body">
              <h6>{{ $p->title }}</h6>
              <a href="{{ route('properties.edit',$p->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>
@endsection
