@extends('layouts/layoutMaster')

@section('title', $product->name)

@section('page-style')
<style>
  .product-gallery-main img {
    width: 100%; max-height: 450px; object-fit: contain;
    border-radius: .75rem; background: #f8f9fa;
  }
  .product-thumbnails img {
    width: 70px; height: 70px; object-fit: cover;
    border-radius: .5rem; cursor: pointer; border: 2px solid transparent;
    transition: border-color .2s;
  }
  .product-thumbnails img.active,
  .product-thumbnails img:hover { border-color: var(--bs-primary); }
  .qty-input { width: 60px; text-align: center; }
  .review-stars i { font-size: 1rem; }
  .vendor-card img { width: 56px; height: 56px; border-radius: 50%; object-fit: cover; }
  .breadcrumb-item a { text-decoration: none; }
</style>
@endsection

@section('content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
    @if($product->category ?? false)
      <li class="breadcrumb-item">
        <a href="{{ url('/products?category=' . $product->category) }}">{{ $product->category }}</a>
      </li>
    @endif
    <li class="breadcrumb-item active">{{ Str::limit($product->name, 40) }}</li>
  </ol>
</nav>

<div class="row g-5">

  {{-- Left: Image Gallery --}}
  <div class="col-lg-6">
    <div class="product-gallery-main mb-3">
      @php
        $mainImg = $product->image ? asset('storage/' . $product->image) : Vite::asset('resources/images/pages/puma-shoes.jpeg');
      @endphp
      <img id="main-product-img" src="{{ $mainImg }}" alt="{{ $product->name }}" />
    </div>

    @if(false)
    <div class="product-thumbnails d-flex flex-wrap gap-2">
      @foreach($images as $idx => $img)
        <img src="{{ asset('storage/' . $img) }}"
             alt="Image {{ $idx + 1 }}"
             class="{{ $idx === 0 ? 'active' : '' }}"
             onclick="switchImage(this, '{{ asset('storage/' . $img) }}')" />
      @endforeach
    </div>
    @else
    {{-- Fallback decorative thumbnails from resources/images --}}
    <div class="product-thumbnails d-flex flex-wrap gap-2">
      <img src="{{ Vite::asset('resources/images/pages/puma-shoes.jpeg') }}"
           alt="Product view 1" class="active"
           onclick="switchImage(this, '{{ Vite::asset('resources/images/pages/puma-shoes.jpeg') }}')" />
      <img src="{{ Vite::asset('resources/images/pages/iphone-11.png') }}"
           alt="Product view 2"
           onclick="switchImage(this, '{{ Vite::asset('resources/images/pages/iphone-11.png') }}')" />
      <img src="{{ Vite::asset('resources/images/pages/google-home.png') }}"
           alt="Product view 3"
           onclick="switchImage(this, '{{ Vite::asset('resources/images/pages/google-home.png') }}')" />
    </div>
    @endif

    {{-- Trust badges --}}
    <div class="d-flex flex-wrap gap-3 mt-4">
      <div class="d-flex align-items-center gap-2 text-muted small">
        <img src="{{ Vite::asset('resources/images/svg/Check.svg') }}" style="width:18px;" alt="Secure" />
        Secure Checkout
      </div>
      <div class="d-flex align-items-center gap-2 text-muted small">
        <img src="{{ Vite::asset('resources/images/svg/rocket.svg') }}" style="width:18px;" alt="Fast" />
        Fast Delivery
      </div>
      <div class="d-flex align-items-center gap-2 text-muted small">
        <img src="{{ Vite::asset('resources/images/svg/Wallet.svg') }}" style="width:18px;" alt="Payment" />
        Easy Returns
      </div>
    </div>
  </div>

  {{-- Right: Product Info --}}
  <div class="col-lg-6">

    {{-- Category badge --}}
    @if($product->category ?? false)
      <span class="badge bg-label-primary mb-2">{{ $product->category }}</span>
    @endif

    <h2 class="fw-bold mb-2">{{ $product->name }}</h2>

    {{-- Rating --}}
    <div class="d-flex align-items-center gap-2 mb-3">
      <div class="review-stars">
        @for($i = 1; $i <= 5; $i++)
          <i class="ti ti-star{{ $i <= ($product->avg_rating ?? 4) ? '-filled text-warning' : ' text-muted' }}"></i>
        @endfor
      </div>
      <span class="text-muted small">({{ $product->reviews_count ?? 0 }} reviews)</span>
      <span class="text-muted small">|</span>
      <span class="text-muted small">{{ $product->orders_count ?? 0 }} sold</span>
    </div>

    {{-- Price --}}
    <div class="d-flex align-items-baseline gap-3 mb-4">
      <h3 class="text-primary fw-bold mb-0">${{ number_format($product->selling_price ?? $product->price ?? 0, 2) }}</h3>
      @if($product->cost_price ?? false)
        <span class="text-muted text-decoration-line-through fs-5">
          ${{ number_format($product->cost_price, 2) }}
        </span>
      @endif
    </div>

    {{-- Stock status --}}
    @php $stk = $product->stock ?? $product->stock_quantity ?? 0; @endphp
    <div class="mb-3">
      @if($stk > 0)
        <span class="badge bg-label-success">
          <i class="ti ti-circle-check me-1"></i>
          In Stock ({{ $stk }} available)
        </span>
      @else
        <span class="badge bg-label-danger">
          <i class="ti ti-circle-x me-1"></i> Out of Stock
        </span>
      @endif
    </div>

    <hr />

    {{-- Description --}}
    <div class="mb-4">
      <h6 class="fw-bold">Description</h6>
      <p class="text-muted">{{ $product->description ?? 'No description available.' }}</p>
    </div>

    {{-- Add to Cart form --}}
    @if(($product->stock_quantity ?? 1) > 0)
    @auth
    <form action="{{ route('cart.add', $product) }}" method="POST" class="mb-3">
      @csrf
      <div class="d-flex align-items-center gap-3 mb-3">
        <label class="fw-semibold mb-0">Quantity:</label>
        <div class="input-group" style="width:120px">
          <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeQty(-1)">
            <i class="ti ti-minus"></i>
          </button>
          <input type="number" name="quantity" id="qty-input" class="form-control qty-input form-control-sm"
                 value="1" min="1" max="{{ $product->stock_quantity ?? 99 }}" />
          <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeQty(1)">
            <i class="ti ti-plus"></i>
          </button>
        </div>
      </div>
      <div class="d-flex gap-3">
        <button type="submit" class="btn btn-primary flex-grow-1">
          <i class="ti ti-shopping-cart me-2"></i>Add to Cart
        </button>
        <a href="{{ route('checkout.index') }}" class="btn btn-success">
          <i class="ti ti-bolt me-1"></i>Buy Now
        </a>
      </div>
    </form>
    @else
    <div class="alert alert-info d-flex align-items-center gap-2">
      <i class="ti ti-info-circle"></i>
      <span>Please <a href="{{ url('/login') }}" class="alert-link">login</a> to add items to cart.</span>
    </div>
    @endauth
    @endif

    {{-- Vendor info --}}
    @if($product->vendor ?? false)
    <div class="vendor-card card bg-body-tertiary border-0 mt-4 p-3">
      <div class="d-flex align-items-center gap-3">
        <img src="{{ $product->vendor->logo ? asset('storage/' . $product->vendor->logo) : Vite::asset('resources/images/logos/logo-1.png') }}"
             onerror="this.src='{{ Vite::asset('resources/images/misc/misc-1.png') }}'"
             alt="{{ $product->vendor->name }}" />
        <div>
          <h6 class="mb-0">{{ $product->vendor->name }}</h6>
          <small class="text-muted">Verified Vendor</small>
        </div>
        <div class="ms-auto">
          <a href="{{ url('/products?vendor=' . $product->vendor->id) }}"
             class="btn btn-sm btn-outline-primary">View Store</a>
        </div>
      </div>
    </div>
    @endif
  </div>
</div>

{{-- Reviews Section --}}
<div class="mt-5">
  <h4 class="fw-bold mb-4">Customer Reviews</h4>

  @if(isset($product->reviews) && $product->reviews->count())
    @foreach($product->reviews as $review)
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="d-flex align-items-start gap-3">
          <div class="avatar">
            @if($review->user->avatar ?? false)
              <img src="{{ asset('storage/' . $review->user->avatar) }}" alt="{{ $review->user->name }}"
                   class="rounded-circle" style="width:40px;height:40px;object-fit:cover;" />
            @else
              @php $avatarIndex = ($loop->index % 14) + 1; @endphp
              <img src="{{ Vite::asset('resources/images/avatars/avatar-' . $avatarIndex . '.png') }}"
                   onerror="this.src='{{ Vite::asset('resources/images/misc/misc-1.png') }}'"
                   alt="{{ $review->user->name }}"
                   class="rounded-circle" style="width:40px;height:40px;object-fit:cover;" />
            @endif
          </div>
          <div class="flex-grow-1">
            <div class="d-flex align-items-center justify-content-between">
              <h6 class="mb-0">{{ $review->user->name }}</h6>
              <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
            </div>
            <div class="review-stars my-1">
              @for($i = 1; $i <= 5; $i++)
                <i class="ti ti-star{{ $i <= $review->rating ? '-filled text-warning' : ' text-muted' }}"></i>
              @endfor
            </div>
            <p class="mb-0 text-muted">{{ $review->comment }}</p>
          </div>
        </div>
      </div>
    </div>
    @endforeach
  @else
    <div class="text-center py-4 text-muted">
      <img src="{{ Vite::asset('resources/images/svg/paper-send.svg') }}"
           alt="No reviews" style="width:60px;opacity:.4" class="mb-3" />
      <p>No reviews yet. Be the first to review this product!</p>
    </div>
  @endif
</div>

{{-- Related Products --}}
@if(isset($relatedProducts) && $relatedProducts->count())
<div class="mt-5">
  <h4 class="fw-bold mb-4">You May Also Like</h4>
  <div class="row g-3">
    @foreach($relatedProducts->take(4) as $related)
    <div class="col-sm-6 col-lg-3">
      <a href="{{ route('products.show', $related->id) }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm h-100">
       @if($related->image && file_exists(public_path('storage/' . $related->image)))
  <img src="{{ asset('storage/' . $related->image) }}" alt="{{ $related->name }}"
       class="card-img-top" style="height:160px;object-fit:cover;" />
@else
  <img src="{{ Vite::asset('resources/images/pages/puma-shoes.jpeg') }}"
       alt="{{ $related->name }}"
       class="card-img-top" style="height:160px;object-fit:cover;" />
@endif
          <div class="card-body p-3">
            <p class="card-title mb-1 fw-semibold text-body small">{{ Str::limit($related->name, 40) }}</p>
            <span class="text-primary fw-bold">${{ number_format($related->price, 2) }}</span>
          </div>
        </div>
      </a>
    </div>
    @endforeach
  </div>
</div>
@endif

@endsection

@section('page-script')
<script>
  function switchImage(thumb, src) {
    document.getElementById('main-product-img').src = src;
    document.querySelectorAll('.product-thumbnails img').forEach(t => t.classList.remove('active'));
    thumb.classList.add('active');
  }

  function changeQty(delta) {
    const input = document.getElementById('qty-input');
    const newVal = parseInt(input.value || 1) + delta;
    const min = parseInt(input.min) || 1;
    const max = parseInt(input.max) || 999;
    input.value = Math.min(Math.max(newVal, min), max);
  }
</script>
@endsection
