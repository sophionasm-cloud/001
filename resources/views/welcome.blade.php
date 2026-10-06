@extends('layouts/blankLayout')

@section('title', 'Welcome')

@section('page-style')
<style>
  .hero-section { min-height: 100vh; display: flex; align-items: center; position: relative; overflow: hidden; }
  .hero-section::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url('{{ Vite::asset("resources/images/pages/background-1.jpg") }}') center/cover no-repeat;
    opacity: .15;
  }
  .hero-content { position: relative; z-index: 1; }
  .feature-card { border: none; transition: transform .2s, box-shadow .2s; }
  .feature-card:hover { transform: translateY(-6px); box-shadow: 0 12px 32px rgba(0,0,0,.12) !important; }
  .category-card { border-radius: 1rem; overflow: hidden; position: relative; cursor: pointer; }
  .category-card img { width: 100%; height: 180px; object-fit: cover; transition: transform .4s; }
  .category-card:hover img { transform: scale(1.08); }
  .category-card .overlay { position: absolute; inset: 0; background: linear-gradient(transparent 40%, rgba(0,0,0,.7)); display: flex; align-items: flex-end; padding: 1.2rem; }
  .front-navbar { position: fixed; top: 0; left: 0; right: 0; z-index: 100; backdrop-filter: blur(10px); background: rgba(255,255,255,.9); border-bottom: 1px solid rgba(0,0,0,.08); }
  body { padding-top: 64px; }
</style>
@endsection

@section('content')

{{-- Front Navbar --}}
<nav class="front-navbar px-4 py-3 d-flex align-items-center justify-content-between">
  <a href="{{ url('/') }}" class="text-decoration-none d-flex align-items-center gap-2">
    <img src="{{ Vite::asset('resources/images/logo.svg') }}"
         onerror="this.src='{{ Vite::asset('resources/images/logos/logo-1.png') }}';this.onerror=null;"
         alt="MultiVendor" style="height:32px;" />
    <span class="fw-bold fs-5">MultiVendor</span>
  </a>
  <div class="d-flex align-items-center gap-3">
    <a href="{{ route('products.index') }}" class="text-body text-decoration-none">Shop</a>
    @auth
      <a href="{{ url('/customer/dashboard') }}" class="btn btn-primary btn-sm">My Account</a>
    @else
      <a href="{{ url('/login') }}" class="btn btn-outline-primary btn-sm">Login</a>
      <a href="{{ url('/register') }}" class="btn btn-primary btn-sm">Sign Up</a>
    @endauth
    <a href="{{ route('cart.view') }}" class="btn btn-icon btn-outline-secondary btn-sm">
      <i class="ti ti-shopping-cart"></i>
    </a>
  </div>
</nav>

{{-- Hero Section --}}
<section class="hero-section py-5">
  <div class="container hero-content">
    <div class="row align-items-center">
      <div class="col-lg-6">
        <div class="badge bg-label-primary mb-3 px-3 py-2">
          <i class="ti ti-bolt me-1"></i>New arrivals every day
        </div>
        <h1 class="display-4 fw-bold mb-4 lh-sm">
          Shop from<br>
          <span class="text-primary">1000+ Vendors</span><br>
          One Platform
        </h1>
        <p class="lead text-muted mb-5">
          Discover millions of products from trusted vendors worldwide.
          Best prices, fast delivery, and easy returns.
        </p>
        <div class="d-flex gap-3 flex-wrap">
          <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg px-5">
            <i class="ti ti-shopping-bag me-2"></i>Start Shopping
          </a>
          <a href="{{ url('/register') }}" class="btn btn-outline-secondary btn-lg px-5">
            Become a Vendor
          </a>
        </div>

        {{-- Stats --}}
        <div class="d-flex gap-4 mt-5">
          <div>
            <h4 class="fw-bold text-primary mb-0">50K+</h4>
            <div class="text-muted small">Products</div>
          </div>
          <div>
            <h4 class="fw-bold text-primary mb-0">1K+</h4>
            <div class="text-muted small">Vendors</div>
          </div>
          <div>
            <h4 class="fw-bold text-primary mb-0">100K+</h4>
            <div class="text-muted small">Customers</div>
          </div>
        </div>
      </div>
      <div class="col-lg-6 d-none d-lg-block">
        <div class="position-relative">
          <img src="{{ Vite::asset('resources/images/pages/girl-using-mobile.png') }}"
               alt="Shopping" class="img-fluid" style="max-height:500px;" />
          {{-- Floating cards --}}
          <div class="card border-0 shadow position-absolute" style="top:10%;right:0;width:160px;">
            <div class="card-body p-3 d-flex align-items-center gap-2">
              <img src="{{ Vite::asset('resources/images/svg/cart.svg') }}" style="width:28px;" />
              <div>
                <div class="fw-bold small">1.2K</div>
                <div class="text-muted" style="font-size:.7rem;">Orders today</div>
              </div>
            </div>
          </div>
          <div class="card border-0 shadow position-absolute" style="bottom:15%;left:0;width:170px;">
            <div class="card-body p-3 d-flex align-items-center gap-2">
              <img src="{{ Vite::asset('resources/images/svg/trending.svg') }}" style="width:28px;" />
              <div>
                <div class="fw-bold small text-success">+28%</div>
                <div class="text-muted" style="font-size:.7rem;">Sales this week</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Categories --}}
<section class="py-5 bg-body-tertiary">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold">Browse by Category</h2>
      <p class="text-muted">Explore our wide range of product categories</p>
    </div>
    <div class="row g-4">
      @php
        $cats = [
          ['name' => 'Electronics',    'img' => 'resources/images/pages/iphone-11.png',     'url' => '?category=electronics'],
          ['name' => 'Fashion',        'img' => 'resources/images/pages/puma-shoes.jpeg',   'url' => '?category=fashion'],
          ['name' => 'Home & Garden',  'img' => 'resources/images/pages/tree-pot.png',      'url' => '?category=home'],
          ['name' => 'Smart Home',     'img' => 'resources/images/pages/google-home.png',   'url' => '?category=smart-home'],
        ];
      @endphp
      @foreach($cats as $cat)
      <div class="col-sm-6 col-lg-3">
        <a href="{{ route('products.index') }}{{ $cat['url'] }}" class="text-decoration-none">
          <div class="category-card shadow-sm">
            <img src="{{ Vite::asset($cat['img']) }}" alt="{{ $cat['name'] }}" />
            <div class="overlay">
              <h5 class="text-white fw-bold mb-0">{{ $cat['name'] }}</h5>
            </div>
          </div>
        </a>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- Featured Products --}}
@if(isset($featuredProducts) && $featuredProducts->count())
<section class="py-5">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <h2 class="fw-bold mb-0">Featured Products</h2>
      <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-sm">View All</a>
    </div>
    <div class="row g-4">
      @foreach($featuredProducts->take(8) as $product)
      <div class="col-sm-6 col-lg-3">
        <div class="card feature-card shadow-sm h-100">
          @if($product->images && count($product->images))
            <img src="{{ asset('storage/' . $product->images[0]) }}"
                 alt="{{ $product->name }}" class="card-img-top" style="height:200px;object-fit:cover;" />
          @else
            <img src="{{ Vite::asset('resources/images/pages/puma-shoes.jpeg') }}"
                 alt="{{ $product->name }}" class="card-img-top" style="height:200px;object-fit:cover;" />
          @endif
          <div class="card-body">
            <h6 class="fw-semibold mb-1">{{ Str::limit($product->name, 35) }}</h6>
            <div class="d-flex align-items-center justify-content-between">
              <span class="fw-bold text-primary">${{ number_format($product->selling_price ?? $product->price ?? 0, 2) }}</span>
              <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-outline-primary">
                View
              </a>
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- Why Choose Us --}}
<section class="py-5 bg-body-tertiary">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold">Why Shop With Us?</h2>
    </div>
    <div class="row g-4">
      @php
        $features = [
          ['title'=>'Fast Delivery',   'desc'=>'Get your orders delivered within 2-5 business days',         'img'=>'resources/images/svg/rocket.svg',      'color'=>'primary'],
          ['title'=>'Secure Payment',  'desc'=>'Your payments are encrypted and 100% secure',                'img'=>'resources/images/svg/Card.svg',        'color'=>'success'],
          ['title'=>'Easy Returns',    'desc'=>'30-day hassle-free return and refund policy',                'img'=>'resources/images/svg/gift.svg',        'color'=>'warning'],
          ['title'=>'24/7 Support',    'desc'=>'Our customer support team is always here to help you',      'img'=>'resources/images/svg/lightbulb.svg',   'color'=>'info'],
        ];
      @endphp
      @foreach($features as $feat)
      <div class="col-sm-6 col-lg-3">
        <div class="card feature-card border-0 shadow-sm text-center p-4 h-100">
          <div class="avatar avatar-xl bg-label-{{ $feat['color'] }} rounded mx-auto mb-3">
            <img src="{{ Vite::asset($feat['img']) }}" style="width:32px;" alt="{{ $feat['title'] }}" />
          </div>
          <h6 class="fw-bold mb-2">{{ $feat['title'] }}</h6>
          <p class="text-muted small mb-0">{{ $feat['desc'] }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- Become a Vendor CTA --}}
<section class="py-5">
  <div class="container">
    <div class="card border-0 shadow overflow-hidden"
         style="background:linear-gradient(135deg,#667eea,#764ba2);">
      <div class="card-body p-5">
        <div class="row align-items-center">
          <div class="col-lg-7 text-white">
            <h2 class="fw-bold mb-3">Become a Vendor Today!</h2>
            <p class="opacity-75 mb-4">
              Join our growing marketplace and reach millions of customers.
              Easy setup, powerful tools, competitive fees.
            </p>
            <div class="d-flex gap-3 flex-wrap mb-4">
              @foreach(['Free to join','Instant approval','24/7 support','Low commission'] as $b)
              <div class="d-flex align-items-center gap-2 text-white">
                <img src="{{ Vite::asset('resources/images/svg/Check.svg') }}"
                     style="width:18px;filter:brightness(0) invert(1);" alt="✓" />
                <span>{{ $b }}</span>
              </div>
              @endforeach
            </div>
            <a href="{{ url('/register') }}" class="btn btn-light btn-lg px-5">
              <i class="ti ti-store me-2"></i>Open Your Store
            </a>
          </div>
          <div class="col-lg-5 text-center d-none d-lg-block">
            <img src="{{ Vite::asset('resources/images/pages/instructor-poster.png') }}"
                 onerror="this.src='{{ Vite::asset('resources/images/pages/boy-illustration.png') }}'"
                 alt="Vendor" style="max-height:220px;opacity:.9;" />
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Footer --}}
<footer class="py-4 bg-body-tertiary border-top">
  <div class="container d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div class="d-flex align-items-center gap-2">
      <img src="{{ Vite::asset('resources/images/logo.svg') }}"
           onerror="this.style.display='none'"
           alt="Logo" style="height:24px;" />
      <span class="fw-semibold">MultiVendor</span>
    </div>
    <div class="text-muted small">
      © {{ date('Y') }} MultiVendor Store. All rights reserved.
    </div>
    <div class="d-flex gap-3">
      <a href="{{ url('/products') }}" class="text-muted small text-decoration-none">Shop</a>
      <a href="{{ url('/login') }}" class="text-muted small text-decoration-none">Login</a>
      <a href="{{ url('/register') }}" class="text-muted small text-decoration-none">Register</a>
      <a href="{{ url('/vendor/login') }}" class="text-muted small text-decoration-none">Vendor</a>
    </div>
  </div>
</footer>

@endsection
