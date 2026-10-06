@extends('layouts/layoutMaster')

@section('title', 'Products')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/swiper/swiper.scss'
])
@endsection

@section('page-style')
<style>
  .product-card { transition: transform 0.2s, box-shadow 0.2s; }
  .product-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,.12) !important; }
  .product-img-wrap { position: relative; overflow: hidden; border-radius: .5rem .5rem 0 0; }
  .product-img-wrap img { width: 100%; height: 220px; object-fit: cover; transition: transform .4s; }
  .product-card:hover .product-img-wrap img { transform: scale(1.05); }
  .badge-discount { position: absolute; top: 10px; left: 10px; }
  .product-rating i { font-size: .85rem; }
  .filter-sidebar .form-check-label { cursor: pointer; }
  .category-img { width: 48px; height: 48px; object-fit: cover; border-radius: 50%; }
  .hero-banner { border-radius: 1rem; overflow: hidden; position: relative; min-height: 280px; }
  .hero-banner img { width: 100%; height: 280px; object-fit: cover; }
  .hero-banner-overlay { position: absolute; inset: 0; background: linear-gradient(90deg, rgba(0,0,0,.6) 40%, transparent); display: flex; align-items: center; padding: 2rem 3rem; }
</style>
@endsection

@section('content')

{{-- Hero Banner --}}
<div class="hero-banner mb-5">
  <img src="{{ Vite::asset('resources/images/banner/banner-1.png') }}"
       onerror="this.src='{{ Vite::asset('resources/images/pages/background-1.jpg') }}'"
       alt="Shop Banner" />
  <div class="hero-banner-overlay">
    <div class="text-white">
      <h1 class="display-5 fw-bold mb-2">Shop Everything</h1>
      <p class="lead mb-4 opacity-75">Discover thousands of products from top vendors</p>
      <a href="#products-grid" class="btn btn-primary btn-lg px-5">
        <i class="ti ti-shopping-bag me-2"></i>Shop Now
      </a>
    </div>
  </div>
</div>

{{-- Category Quick Links --}}
<div class="mb-5">
  <h5 class="fw-bold mb-3">Browse Categories</h5>
  <div class="row g-3">
    @php
      $categories = [
        ['label' => 'Electronics', 'icon' => 'ti-device-laptop', 'img' => 'resources/images/svg/laptop.svg', 'color' => 'primary'],
        ['label' => 'Fashion',     'icon' => 'ti-shirt',         'img' => 'resources/images/svg/Suitcase.svg','color' => 'info'],
        ['label' => 'Home',        'icon' => 'ti-home',          'img' => 'resources/images/svg/home.svg',   'color' => 'success'],
        ['label' => 'Payments',    'icon' => 'ti-credit-card',   'img' => 'resources/images/svg/payment.svg','color' => 'warning'],
        ['label' => 'Gifts',       'icon' => 'ti-gift',          'img' => 'resources/images/svg/gift.svg',   'color' => 'danger'],
        ['label' => 'Trending',    'icon' => 'ti-trending-up',   'img' => 'resources/images/svg/trending.svg','color' => 'secondary'],
      ];
    @endphp
    @foreach($categories as $cat)
    <div class="col-6 col-sm-4 col-md-2">
      <a href="{{ url('/products?category=' . strtolower($cat['label'])) }}"
         class="card text-center p-3 text-decoration-none border-0 shadow-sm h-100">
        <div class="avatar avatar-lg mx-auto mb-2 bg-label-{{ $cat['color'] }} rounded-circle">
          <img src="{{ Vite::asset($cat['img']) }}" alt="{{ $cat['label'] }}"
               style="width:28px;height:28px;filter:none;" />
        </div>
        <small class="fw-semibold text-body">{{ $cat['label'] }}</small>
      </a>
    </div>
    @endforeach
  </div>
</div>

{{-- Products Grid + Filters --}}
<div id="products-grid" class="row g-4">

  {{-- Filter Sidebar --}}
  <div class="col-lg-3">
    <div class="card shadow-sm sticky-top" style="top:80px">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-bold">Filters</h6>
        <a href="{{ url('/products') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
      </div>
      <div class="card-body">
        <form method="GET" action="{{ url('/products') }}" id="filter-form">

          {{-- Search --}}
          <div class="mb-4">
            <label class="form-label fw-semibold">Search</label>
            <div class="input-group">
              <span class="input-group-text"><i class="ti ti-search"></i></span>
              <input type="text" name="q" class="form-control" placeholder="Search products…"
                     value="{{ request('q') }}" />
            </div>
          </div>

          {{-- Price Range --}}
          <div class="mb-4">
            <label class="form-label fw-semibold">Price Range</label>
            <div class="row g-2">
              <div class="col-6">
                <input type="number" name="min_price" class="form-control form-control-sm"
                       placeholder="Min" value="{{ request('min_price') }}" />
              </div>
              <div class="col-6">
                <input type="number" name="max_price" class="form-control form-control-sm"
                       placeholder="Max" value="{{ request('max_price') }}" />
              </div>
            </div>
          </div>

          {{-- Sort --}}
          <div class="mb-4">
            <label class="form-label fw-semibold">Sort By</label>
            <select name="sort" class="form-select form-select-sm">
              <option value="">Default</option>
              <option value="price_asc"  {{ request('sort') == 'price_asc'  ? 'selected' : '' }}>Price: Low to High</option>
              <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
              <option value="newest"     {{ request('sort') == 'newest'     ? 'selected' : '' }}>Newest First</option>
              <option value="popular"    {{ request('sort') == 'popular'    ? 'selected' : '' }}>Most Popular</option>
            </select>
          </div>

          <button type="submit" class="btn btn-primary w-100">
            <i class="ti ti-filter me-1"></i> Apply Filters
          </button>
        </form>
      </div>
    </div>
  </div>

  {{-- Products --}}
  <div class="col-lg-9">

    {{-- Results bar --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
      <p class="mb-0 text-muted">
        Showing <strong>{{ $products->firstItem() ?? 0 }}</strong>–<strong>{{ $products->lastItem() ?? 0 }}</strong>
        of <strong>{{ $products->total() }}</strong> products
      </p>
      <div class="d-flex gap-2">
        <a href="?{{ http_build_query(array_merge(request()->query(), ['view' => 'grid'])) }}"
           class="btn btn-sm btn-{{ request('view','grid') == 'grid' ? 'primary' : 'outline-secondary' }}">
          <i class="ti ti-layout-grid"></i>
        </a>
        <a href="?{{ http_build_query(array_merge(request()->query(), ['view' => 'list'])) }}"
           class="btn btn-sm btn-{{ request('view') == 'list' ? 'primary' : 'outline-secondary' }}">
          <i class="ti ti-list"></i>
        </a>
      </div>
    </div>

    @if($products->isEmpty())
      <div class="text-center py-5">
        <img src="{{ Vite::asset('resources/images/pages/empty-cart.png') }}"
             alt="No products" style="max-width:200px" class="mb-3 opacity-75" />
        <h5 class="text-muted">No products found</h5>
        <p class="text-muted">Try adjusting your filters or search terms.</p>
        <a href="{{ url('/products') }}" class="btn btn-primary">View All Products</a>
      </div>
    @else
      <div class="row g-4">
        @foreach($products as $product)
        <div class="col-sm-6 col-xl-4">
          <div class="card product-card h-100 shadow-sm border-0">

            {{-- Product Image --}}
            <div class="product-img-wrap">
              @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}"
                     alt="{{ $product->name }}" />
              @else
                <img src="{{ Vite::asset('resources/images/ecommerce-images/product-' . (($loop->index % 5) + 1) . '.png') }}"
                     onerror="this.src='{{ Vite::asset('resources/images/pages/puma-shoes.jpeg') }}'"
                     alt="{{ $product->name }}" />
              @endif

              @if($product->discount_percent ?? false)
                <span class="badge bg-danger badge-discount">
                  -{{ $product->discount_percent }}%
                </span>
              @endif

              {{-- Quick add to cart on hover --}}
              <div class="position-absolute bottom-0 start-0 end-0 p-2 d-flex gap-2"
                   style="background:linear-gradient(transparent,rgba(0,0,0,.5));">
                @auth
                  <form action="{{ route('cart.add', $product) }}" method="POST" class="flex-grow-1">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                      <i class="ti ti-shopping-cart me-1"></i>Add to Cart
                    </button>
                  </form>
                @else
                  <a href="{{ url('/login') }}" class="btn btn-primary btn-sm flex-grow-1">
                    <i class="ti ti-shopping-cart me-1"></i>Add to Cart
                  </a>
                @endauth
              </div>
            </div>

            <div class="card-body d-flex flex-column">
              {{-- Vendor badge --}}
              @if($product->vendor ?? false)
              <div class="d-flex align-items-center gap-2 mb-2">
                <img src="{{ $product->vendor->logo ? asset('storage/' . $product->vendor->logo) : Vite::asset('resources/images/logos/logo-1.png') }}"
                     onerror="this.src='{{ Vite::asset('resources/images/misc/misc-1.png') }}'"
                     alt="{{ $product->vendor->name }}"
                     style="width:20px;height:20px;border-radius:50%;object-fit:cover;" />
                <small class="text-muted">{{ $product->vendor->name }}</small>
              </div>
              @endif

              <h6 class="card-title mb-1">
                <a href="{{ route('products.show', $product->id) }}"
                   class="text-body text-decoration-none stretched-link">
                  {{ $product->name }}
                </a>
              </h6>
              <p class="text-muted small mb-2 flex-grow-1">
                {{ Str::limit($product->description, 80) }}
              </p>

              {{-- Rating --}}
              <div class="product-rating mb-2 d-flex align-items-center gap-1">
                @for($i = 1; $i <= 5; $i++)
                  <i class="ti ti-star{{ $i <= ($product->avg_rating ?? 4) ? '-filled text-warning' : ' text-muted' }}"></i>
                @endfor
                <small class="text-muted">({{ $product->reviews_count ?? 0 }})</small>
              </div>

              {{-- Price --}}
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <span class="fw-bold text-primary fs-5">
                    ${{ number_format($product->selling_price ?? $product->price ?? 0, 2) }}
                  </span>
                  @if($product->cost_price ?? false)
                    <small class="text-muted text-decoration-line-through ms-1">
                      ${{ number_format($product->cost_price, 2) }}
                    </small>
                  @endif
                </div>
                @php $stk = $product->stock ?? $product->stock_quantity ?? 0; @endphp
                @if($stk <= 5 && $stk > 0)
                  <span class="badge bg-label-warning">Only {{ $stk }} left</span>
                @elseif($stk == 0)
                  <span class="badge bg-label-danger">Out of Stock</span>
                @else
                  <span class="badge bg-label-success">In Stock</span>
                @endif
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>

      {{-- Pagination --}}
      <div class="d-flex justify-content-center mt-5">
        {{ $products->withQueryString()->links() }}
      </div>
    @endif
  </div>
</div>

@endsection

@section('page-script')
<script>
  // Auto-submit filter form on sort change
  document.querySelector('[name="sort"]')?.addEventListener('change', function() {
    this.closest('form').submit();
  });
</script>
@endsection
