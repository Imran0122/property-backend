@include('layouts.navbar')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hectare - Home</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Arial', sans-serif; }

        /* Hero Section */
        .hero {
            background: url('https://www.zameen.com/assets/homepage_cover_web-18f1893d63e84f1b83cb0e7da0a75b6d.jpg') no-repeat center center;
            background-size: cover;
            height: 80vh;
            position: relative;
        }
        .hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.5);
        }
        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 900px;
            margin: 0 auto;
            padding: 0 15px;
            color: #fff;
        }
        .hero-content h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .hero-content p {
            font-size: 1.25rem;
            margin-bottom: 1.5rem;
        }

        /* Filter Box */
        .filter-box {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-top: -50px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .filter-tabs .nav-link {
            color: #6c757d;
            font-weight: 500;
            transition: all 0.3s;
        }
        .filter-tabs .nav-link.active {
            color: #28a745 !important;
            border-bottom: 2px solid #28a745;
        }
        .form-control {
            border-radius: 50px;
        }
        .btn-search {
            border-radius: 50px;
            background-color: #28a745;
            color: #fff;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-search:hover {
            background-color: #218838;
        }
        .more-filters a {
            color: #28a745;
            cursor: pointer;
            text-decoration: none;
            font-weight: 500;
        }

        /* Second Section */
        .popular-cities .card {
    border-radius: 15px;
    transition: transform 0.3s, box-shadow 0.3s;
}
.popular-cities .card:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 15px 30px rgba(0,0,0,0.2);
}
.popular-cities img {
    height: 180px;
    object-fit: cover;
    transition: transform 0.3s;
}
.popular-cities .card:hover img {
    transform: scale(1.1);
}

        
        @media (max-width: 768px) {
            .hero { height: 60vh; }
            .hero-content h1 { font-size: 2rem; }
            .hero-content p { font-size: 1rem; }
            .filter-box { padding: 15px; margin-top: -30px; }
            .form-control { margin-bottom: 10px; }
        }
    </style>
</head>
<body>

<section class="hero d-flex align-items-center">
    <div class="container hero-content">
        <h1>Search Properties in Pakistan – Buy, Rent & More</h1>
        <p>Find homes, plots, commercial properties & projects</p>

        <div class="filter-box">
            <!-- Tabs -->
            <ul class="nav nav-tabs filter-tabs justify-content-center mb-3" id="propertyTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="buy-tab" data-bs-toggle="tab" data-bs-target="#buy" type="button" role="tab">Buy</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="rent-tab" data-bs-toggle="tab" data-bs-target="#rent" type="button" role="tab">Rent</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="projects-tab" data-bs-toggle="tab" data-bs-target="#projects" type="button" role="tab">Projects</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="plots-tab" data-bs-toggle="tab" data-bs-target="#plots" type="button" role="tab">Plots</button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Buy Tab -->
                <div class="tab-pane fade show active" id="buy" role="tabpanel">
                    <form class="row g-2">
                        <div class="col-md-3">
                            <select class="form-control">
                                <option selected disabled>City</option>
                                <option>Lahore</option>
                                <option>Karachi</option>
                                <option>Islamabad</option>
                                <option>Bhawalpur</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" placeholder="Location">
                        </div>
                        <div class="col-md-3">
                            <select class="form-control">
                                <option selected disabled>Property Type</option>
                                <option>Home</option>
                                <option>Plot</option>
                                <option>Commercial</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control">
                                <option selected disabled>Price Range</option>
                                <option>Up to 50 Lac</option>
                                <option>50-100 Lac</option>
                                <option>1 Crore+</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control">
                                <option selected disabled>Beds</option>
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4+</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" placeholder="Keyword">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-search w-100">Search Properties</button>
                        </div>
                    </form>

                    <div class="mt-2 text-end more-filters">
                        <a data-bs-toggle="collapse" href="#moreFiltersBuy">+ More Filters</a>
                    </div>
                    <div class="collapse mt-3" id="moreFiltersBuy">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <select class="form-control">
                                    <option selected disabled>Furnishing</option>
                                    <option>Furnished</option>
                                    <option>Unfurnished</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control">
                                    <option selected disabled>Construction Status</option>
                                    <option>New</option>
                                    <option>Used</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control">
                                    <option selected disabled>Area (Marla)</option>
                                    <option>1 Marla</option>
                                    <option>2 Marla</option>
                                    <option>5 Marla</option>
                                    <option>10 Marla</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control">
                                    <option selected disabled>Status</option>
                                    <option>For Sale</option>
                                    <option>For Rent</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rent Tab -->
                <div class="tab-pane fade" id="rent" role="tabpanel">
                    <p>Rent filters here...</p>
                </div>

                <div class="tab-pane fade" id="projects" role="tabpanel">
                    <p>Project search filters...</p>
                </div>

                <div class="tab-pane fade" id="plots" role="tabpanel">
                    <p>Plot search filters...</p>
                </div>
            </div>
        </div>
    </div>


   
</section>

{{-- ⭐ Featured Properties Section --}}
<h3 class="text-2xl font-bold mb-4">🏆 Featured Properties</h3>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
  @foreach($featuredProperties as $property)
  <div class="border rounded overflow-hidden shadow">
    <img src="{{ $property->images[0]->url ?? 'https://via.placeholder.com/400x200' }}" class="w-full h-48 object-cover">
    <div class="p-3">
      <h5 class="font-semibold">{{ $property->title }}</h5>
      <p class="text-green-600 font-bold">PKR {{ number_format($property->price) }}</p>
      <p class="text-sm text-gray-500">{{ $property->city->name ?? '' }}</p>
    </div>
  </div>
  @endforeach
</div>


 <!-- next Section----------------------------------- -->
     <!-- Start=============================================== -->


<!-- Browse Properties Section -->
<section class="py-5">
  <div class="container">
    <h2 class="h4 fw-bold mb-4">Browse Properties</h2>

    <div class="row g-4">
      <!-- Homes -->
      <div class="col-md-4">
        <div class="card shadow-sm rounded-3 h-100">
          <div class="card-body">
            <div class="d-flex align-items-center mb-3">
              <i class="fa-solid fa-house text-success fs-4 me-2"></i>
              <h5 class="mb-0">Homes</h5>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs border-0 mb-3" id="homesTab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active text-success fw-semibold" data-bs-toggle="tab" data-bs-target="#homes-popular" type="button" role="tab">Popular</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#homes-type" type="button" role="tab">Type</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#homes-size" type="button" role="tab">Area Size</button>
              </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content">
              <div class="tab-pane fade show active" id="homes-popular" role="tabpanel">
                <div class="row g-2">
                  <div class="col-4"><div class="border rounded p-2 small">5 Marla<br><span class="text-muted">Houses</span></div></div>
                  <div class="col-4"><div class="border rounded p-2 small">10 Marla<br><span class="text-muted">Houses</span></div></div>
                  <div class="col-4"><div class="border rounded p-2 small">3 Marla<br><span class="text-muted">Houses</span></div></div>
                  <div class="col-4"><div class="border rounded p-2 small">New<br><span class="text-muted">Houses</span></div></div>
                  <div class="col-4"><div class="border rounded p-2 small">Low Price<br><span class="text-muted">All Homes</span></div></div>
                  <div class="col-4"><div class="border rounded p-2 small">Small<br><span class="text-muted">Houses</span></div></div>
                </div>
              </div>
              <div class="tab-pane fade" id="homes-type" role="tabpanel">Type content…</div>
              <div class="tab-pane fade" id="homes-size" role="tabpanel">Area size content…</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Plots -->
      <div class="col-md-4">
        <div class="card shadow-sm rounded-3 h-100">
          <div class="card-body">
            <div class="d-flex align-items-center mb-3">
              <i class="fa-solid fa-map text-success fs-4 me-2"></i>
              <h5 class="mb-0">Plots</h5>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs border-0 mb-3" id="plotsTab" role="tablist">
              <li class="nav-item"><button class="nav-link active text-success fw-semibold" data-bs-toggle="tab" data-bs-target="#plots-popular" type="button">Popular</button></li>
              <li class="nav-item"><button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#plots-type" type="button">Type</button></li>
              <li class="nav-item"><button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#plots-size" type="button">Area Size</button></li>
            </ul>

            <!-- Content -->
            <div class="tab-content">
              <div class="tab-pane fade show active" id="plots-popular">
                <div class="row g-2">
                  <div class="col-4"><div class="border rounded p-2 small">5 Marla<br><span class="text-muted">Residential</span></div></div>
                  <div class="col-4"><div class="border rounded p-2 small">10 Marla<br><span class="text-muted">Residential</span></div></div>
                  <div class="col-4"><div class="border rounded p-2 small">3 Marla<br><span class="text-muted">Residential</span></div></div>
                  <div class="col-4"><div class="border rounded p-2 small">On Instalments<br><span class="text-muted">Plots</span></div></div>
                  <div class="col-4"><div class="border rounded p-2 small">With Possession<br><span class="text-muted">Plots</span></div></div>
                  <div class="col-4"><div class="border rounded p-2 small">Corner<br><span class="text-muted">Plots</span></div></div>
                </div>
              </div>
              <div class="tab-pane fade" id="plots-type">Type content…</div>
              <div class="tab-pane fade" id="plots-size">Area size content…</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Commercial -->
      <div class="col-md-4">
        <div class="card shadow-sm rounded-3 h-100">
          <div class="card-body">
            <div class="d-flex align-items-center mb-3">
              <i class="fa-solid fa-building text-success fs-4 me-2"></i>
              <h5 class="mb-0">Commercial</h5>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs border-0 mb-3" id="commTab" role="tablist">
              <li class="nav-item"><button class="nav-link active text-success fw-semibold" data-bs-toggle="tab" data-bs-target="#comm-popular" type="button">Popular</button></li>
              <li class="nav-item"><button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#comm-type" type="button">Type</button></li>
              <li class="nav-item"><button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#comm-size" type="button">Area Size</button></li>
            </ul>

            <!-- Content -->
            <div class="tab-content">
              <div class="tab-pane fade show active" id="comm-popular">
                <div class="row g-2">
                  <div class="col-4"><div class="border rounded p-2 small">Small<br><span class="text-muted">Offices</span></div></div>
                  <div class="col-4"><div class="border rounded p-2 small">New<br><span class="text-muted">Offices</span></div></div>
                  <div class="col-4"><div class="border rounded p-2 small">On Instalments<br><span class="text-muted">Shops</span></div></div>
                  <div class="col-4"><div class="border rounded p-2 small">Small<br><span class="text-muted">Shops</span></div></div>
                  <div class="col-4"><div class="border rounded p-2 small">New<br><span class="text-muted">Shops</span></div></div>
                  <div class="col-4"><div class="border rounded p-2 small">Running<br><span class="text-muted">Shops</span></div></div>
                </div>
              </div>
              <div class="tab-pane fade" id="comm-type">Type content…</div>
              <div class="tab-pane fade" id="comm-size">Area size content…</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>



<!-- next section============================================== -->
 <!-- ==============++++++++++++++++++++++++++++++++++++++++++++++++++ -->
 <!-- Explore More Section -->
<section class="explore-more py-5">
  <div class="container">
    <h3 class="fw-bold mb-4">Explore more on Hectare</h3>

    <div class="row g-4">
      <!-- Card 1 -->
      <div class="col-md-3 col-sm-6">
        <div class="feature-card p-4 text-center h-100">
          <div class="icon bg-warning bg-opacity-25 text-warning mb-3 rounded p-3 mx-auto" style="width:70px; height:70px;">
            <i class="fas fa-building fa-2x"></i>
          </div>
          <h6 class="fw-bold">New Projects</h6>
          <p class="text-muted small">The best investment opportunities</p>
        </div>
      </div>

      <!-- Card 2 -->
      <div class="col-md-3 col-sm-6">
        <div class="feature-card p-4 text-center h-100">
          <div class="icon bg-info bg-opacity-25 text-info mb-3 rounded p-3 mx-auto" style="width:70px; height:70px;">
            <i class="fas fa-tools fa-2x"></i>
          </div>
          <h6 class="fw-bold">Construction Cost Calculator</h6>
          <p class="text-muted small">Get construction cost estimate</p>
        </div>
      </div>

      <!-- Card 3 -->
      <div class="col-md-3 col-sm-6">
        <div class="feature-card p-4 text-center h-100">
          <div class="icon bg-success bg-opacity-25 text-success mb-3 rounded p-3 mx-auto" style="width:70px; height:70px;">
            <i class="fas fa-calculator fa-2x"></i>
          </div>
          <h6 class="fw-bold">Home Loan Calculator</h6>
          <p class="text-muted small">Find affordable loan packages</p>
        </div>
      </div>

      <!-- Card 4 -->
      <div class="col-md-3 col-sm-6">
        <div class="feature-card p-4 text-center h-100">
          <div class="icon bg-danger bg-opacity-25 text-danger mb-3 rounded p-3 mx-auto" style="width:70px; height:70px;">
            <i class="fas fa-map-marked-alt fa-2x"></i>
          </div>
          <h6 class="fw-bold">Area Guides</h6>
          <p class="text-muted small">Explore housing societies in Pakistan</p>
        </div>
      </div>

      <!-- Card 5 -->
      <div class="col-md-3 col-sm-6">
        <div class="feature-card p-4 text-center h-100">
          <div class="icon bg-success bg-opacity-25 text-success mb-3 rounded p-3 mx-auto" style="width:70px; height:70px;">
            <i class="fas fa-map fa-2x"></i>
          </div>
          <h6 class="fw-bold">Plot Finder</h6>
          <p class="text-muted small">Find plots in any housing society</p>
        </div>
      </div>

      <!-- Card 6 -->
      <div class="col-md-3 col-sm-6">
        <div class="feature-card p-4 text-center h-100">
          <div class="icon bg-primary bg-opacity-25 text-primary mb-3 rounded p-3 mx-auto" style="width:70px; height:70px;">
            <i class="fas fa-chart-line fa-2x"></i>
          </div>
          <h6 class="fw-bold">Property Index</h6>
          <p class="text-muted small">Track changes in real estate prices</p>
        </div>
      </div>

      <!-- Card 7 -->
      <div class="col-md-3 col-sm-6">
        <div class="feature-card p-4 text-center h-100">
          <div class="icon bg-secondary bg-opacity-25 text-secondary mb-3 rounded p-3 mx-auto" style="width:70px; height:70px;">
            <i class="fas fa-ruler-combined fa-2x"></i>
          </div>
          <h6 class="fw-bold">Area Unit Converter</h6>
          <p class="text-muted small">Convert any area unit instantly</p>
        </div>
      </div>

      <!-- Card 8 -->
      <div class="col-md-3 col-sm-6">
        <div class="feature-card p-4 text-center h-100">
          <div class="icon bg-purple bg-opacity-25 text-purple mb-3 rounded p-3 mx-auto" style="width:70px; height:70px;">
            <i class="fas fa-chart-bar fa-2x"></i>
          </div>
          <h6 class="fw-bold">Property Trends</h6>
          <p class="text-muted small">Find popular areas to buy property</p>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- ========================================== -->
<!-- Agencies Partner -->



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
