@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- ✅ Breadcrumb + Share --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <small class="text-muted">
            <a href="{{ url('/') }}">Home</a> /
            {{ $property->city->name ?? '' }} /
            {{ $property->title }}
        </small>
        <div>
            <a href="https://facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="btn btn-light btn-sm"><i class="bi bi-facebook"></i></a>
            <a href="https://wa.me/?text={{ urlencode(request()->fullUrl()) }}" target="_blank" class="btn btn-light btn-sm"><i class="bi bi-whatsapp"></i></a>
            <a href="https://twitter.com/share?url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="btn btn-light btn-sm"><i class="bi bi-twitter"></i></a>
        </div>
    </div>

    {{-- ✅ Title + Location + Price + Favorite --}}
    <div class="d-flex justify-content-between align-items-start border-bottom pb-2 mb-3">
        <div>
            <h2 class="fw-bold">
                {{ $property->title }}
                @if($property->status === 'approved')
                    <span class="badge bg-success">Verified</span>
                @endif
            </h2>
            <p class="text-muted mb-1">
                <i class="bi bi-geo-alt-fill"></i> 
                {{ $property->location }},
                {{ $property->city->name ?? '' }}
            </p>
            <p class="mb-1"><strong>Type:</strong> {{ $property->propertyType->name ?? 'N/A' }}</p>
            <p class="text-success fw-bold h4">PKR {{ number_format($property->price) }}</p>
        </div>
        <div>
            @auth
                <button id="favoriteBtn" data-id="{{ $property->id }}" class="btn btn-outline-danger">
                    <i class="bi {{ auth()->user()->favorites->contains($property->id) ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                </button>
            @endauth
        </div>
    </div>

    {{-- ✅ Property Overview --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="border rounded p-3 bg-light">
                <h5 class="fw-bold">Property Overview</h5>
                <div class="row mt-2">
                    <div class="col-md-3"><strong>Property ID:</strong> {{ $property->id }}</div>
                    <div class="col-md-3"><strong>Purpose:</strong> {{ ucfirst($property->purpose ?? 'Sale') }}</div>
                    <div class="col-md-3"><strong>Listed:</strong> {{ $property->created_at->format('d M Y') }}</div>
                    <div class="col-md-3"><strong>Agent:</strong> {{ $property->user->name ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- ✅ Gallery + Details --}}
        <div class="col-md-8">
            {{-- Carousel --}}
            <div id="propertyCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @forelse($property->images as $key => $img)
                        <div class="carousel-item @if($key==0) active @endif">
                            <img src="{{ $img->url ?? asset('storage/'.$img->image_path) }}" class="d-block w-100" style="height:450px; object-fit:cover;" alt="photo">
                        </div>
                    @empty
                        <div class="carousel-item active">
                            <img src="https://via.placeholder.com/800x450" class="d-block w-100" alt="no-image">
                        </div>
                    @endforelse
                </div>
                @if($property->images->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#propertyCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#propertyCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                @endif
            </div>

            {{-- Thumbnails --}}
            @if($property->images->count())
                <div class="d-flex mt-2 flex-wrap">
                    @foreach($property->images as $key => $img)
                        <img src="{{ $img->url ?? asset('storage/'.$img->image_path) }}" 
                             class="img-thumbnail me-2 mb-2" style="height:80px; cursor:pointer;" 
                             onclick="$('#propertyCarousel').carousel({{ $key }})" alt="thumb">
                    @endforeach
                </div>
            @endif

            {{-- Details --}}
            <div class="mt-4">
                <h4>Property Details</h4>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Bedrooms:</strong> {{ $property->bedrooms ?? 'N/A' }}</li>
                    <li class="list-group-item"><strong>Bathrooms:</strong> {{ $property->bathrooms ?? 'N/A' }}</li>
                    <li class="list-group-item"><strong>Area:</strong> {{ $property->area ? $property->area.' sq.ft.' : 'N/A' }}</li>
                    <li class="list-group-item"><strong>Status:</strong> {{ ucfirst($property->status) }}</li>
                </ul>
            </div>

            {{-- Description --}}
            @if($property->description)
                <div class="mt-4">
                    <h4>Description</h4>
                    <p>{{ $property->description }}</p>
                </div>
            @endif

            {{-- Amenities --}}
            <div class="mt-4">
                <h5>Amenities</h5>
                <ul class="list-inline">
                    @forelse($property->amenities as $amenity)
                        <li class="list-inline-item badge bg-light text-dark border">{{ $amenity->name }}</li>
                    @empty
                        <p class="text-muted">No amenities listed</p>
                    @endforelse
                </ul>
            </div>

            {{-- Map --}}
            <div class="mt-4">
                <h5>Location on Map</h5>
                <iframe src="https://maps.google.com/maps?q={{ urlencode($property->location) }}&t=&z=13&ie=UTF8&iwloc=&output=embed"
                        width="100%" height="300" style="border:0;" allowfullscreen></iframe>
            </div>

            {{-- ✅ Mortgage Calculator --}}
            <div class="mt-5">
                <h4>Mortgage Calculator</h4>
                <div class="border rounded p-3">
                    <div class="row g-2">
                        <div class="col-md-4"><input type="number" id="loanAmount" class="form-control" placeholder="Loan Amount (PKR)" value="{{ $property->price }}"></div>
                        <div class="col-md-4"><input type="number" id="interestRate" class="form-control" placeholder="Interest Rate (%)" value="10"></div>
                        <div class="col-md-4"><input type="number" id="loanYears" class="form-control" placeholder="Years" value="20"></div>
                    </div>
                    <button onclick="calculateMortgage()" class="btn btn-primary mt-3">Calculate</button>
                    <div id="mortgageResult" class="mt-2 fw-bold text-success"></div>
                </div>
            </div>

            {{-- ✅ Related Properties --}}
            @if($relatedProperties->count())
                <div class="mt-5">
                    <h4>Similar Properties</h4>
                    <div class="row">
                        @foreach($relatedProperties as $related)
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('properties.show', $related->id) }}" class="text-decoration-none">
                                    <div class="card h-100">
                                        <img src="{{ $related->images[0]->url ?? (isset($related->images[0]) ? asset('storage/'.$related->images[0]->image_path) : 'https://via.placeholder.com/400x200') }}" class="card-img-top" alt="">
                                        <div class="card-body">
                                            <h6 class="fw-bold text-dark">{{ $related->title }}</h6>
                                            <p class="text-success">PKR {{ number_format($related->price) }}</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- ✅ Agent Contact Sidebar --}}
        <div class="col-md-4">
            <div class="border rounded p-3 mb-3">
                <h5 class="fw-bold">{{ $property->user->name ?? 'Agent' }}</h5>
                <p class="small text-muted mb-2">Contact this agent for more details</p>

                <form id="agentForm" method="POST" action="{{ route('leads.store') }}">
                    @csrf
                    <input type="hidden" name="property_id" value="{{ $property->id }}">
                    <div class="mb-2"><input type="text" name="name" class="form-control" placeholder="Your Name" required></div>
                    <div class="mb-2"><input type="email" name="email" class="form-control" placeholder="Your Email"></div>
                    <div class="mb-2"><input type="text" name="phone" class="form-control" placeholder="Your Phone"></div>
                    <div class="mb-2"><textarea name="message" class="form-control" rows="3" placeholder="Message"></textarea></div>
                    <button type="submit" class="btn btn-success w-100">Send Message</button>
                </form>

                @if($property->user->phone)
                    <a href="https://wa.me/{{ $property->user->phone }}?text=Hi, I'm interested in {{ urlencode($property->title) }}" 
                       target="_blank" class="btn btn-outline-success w-100 mt-2">
                        <i class="bi bi-whatsapp"></i> Chat on WhatsApp
                    </a>
                @endif

                {{-- Report Property Button --}}
                <button class="btn btn-outline-danger w-100 mt-2" data-bs-toggle="modal" data-bs-target="#reportModal">
                    Report this Property
                </button>

                <div id="agentMessageResponse" class="mt-2"></div>
            </div>
        </div>
    </div>
</div>

{{-- ✅ Report Modal --}}
<div class="modal fade" id="reportModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="reportForm" method="POST" action="{{ route('reports.store') }}" class="modal-content">
      @csrf
      <input type="hidden" name="property_id" value="{{ $property->id }}">
      <div class="modal-header">
        <h5 class="modal-title">Report Property</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Reason</label>
          <select name="reason" class="form-control" required>
            <option value="">Select reason</option>
            <option value="fake">Fake Listing</</option>
            <option value="sold">Already Sold</option>
            <option value="fraud">Fraud / Scam</option>
            <option value="other">Other</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Message</label>
          <textarea name="message" class="form-control" rows="3"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-danger">Submit Report</button>
      </div>
    </form>
  </div>
</div>

{{-- ✅ Sticky Contact Bar --}}
<div class="d-md-none fixed-bottom bg-white border-top p-2">
    <div class="d-flex justify-content-around">
        <a href="tel:{{ $property->user->phone ?? '#' }}" class="btn btn-outline-primary flex-fill mx-1"><i class="bi bi-telephone"></i> Call</a>
        <a href="https://wa.me/{{ $property->user->phone ?? '' }}" target="_blank" class="btn btn-outline-success flex-fill mx-1"><i class="bi bi-whatsapp"></i> WhatsApp</a>
        <button class="btn btn-success flex-fill mx-1" data-bs-toggle="modal" data-bs-target="#agentFormModal"><i class="bi bi-envelope"></i> Email</button>
    </div>
</div>

@endsection

@push('scripts')
<script>
function calculateMortgage(){
    let P = parseFloat(document.getElementById('loanAmount').value);
    let r = parseFloat(document.getElementById('interestRate').value) / 100 / 12;
    let n = parseInt(document.getElementById('loanYears').value) * 12;
    let EMI = (P*r*Math.pow(1+r,n))/(Math.pow(1+r,n)-1);
    document.getElementById('mortgageResult').innerText = "Monthly Payment: PKR " + Math.round(EMI).toLocaleString();
}

// ✅ Favorite toggle
document.getElementById('favoriteBtn')?.addEventListener('click', function() {
    let propertyId = this.dataset.id;
    fetch(`/properties/${propertyId}/favorite`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if(data.is_favorite){
            this.innerHTML = '<i class="bi bi-heart-fill"></i>';
        } else {
            this.innerHTML = '<i class="bi bi-heart"></i>';
        }
    });
});

// ✅ Lead form AJAX
document.getElementById('agentForm')?.addEventListener('submit', function(e){
    e.preventDefault();
    let formData = new FormData(this);
    fetch(this.action, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('agentMessageResponse').innerHTML = 
            `<div class="alert alert-success">${data.message ?? 'Your inquiry has been sent!'}</div>`;
        this.reset();
    })
    .catch(() => {
        document.getElementById('agentMessageResponse').innerHTML = 
            `<div class="alert alert-danger">Failed to send message</div>`;
    });
});

// ✅ Report form AJAX
document.getElementById('reportForm')?.addEventListener('submit', function(e){
    e.preventDefault();
    let formData = new FormData(this);
    fetch(this.action, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message ?? 'Report submitted!');
        let modal = bootstrap.Modal.getInstance(document.getElementById('reportModal'));
        modal.hide();
        this.reset();
    })
    .catch(() => {
        alert('Failed to submit report');
    });
});
</script>
@endpush
