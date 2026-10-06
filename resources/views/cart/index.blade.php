@extends('layouts/layoutMaster')

@section('title', 'Shopping Cart')

@section('page-style')
<style>
  .cart-item-img { width: 80px; height: 80px; object-fit: cover; border-radius: .5rem; }
  .qty-control { width: 40px; text-align: center; border: none; background: transparent; font-weight: 600; }
  .empty-cart-img { max-width: 250px; opacity: .8; }
  .order-summary-card { position: sticky; top: 80px; }
</style>
@endsection

@section('content')

@php
  $cartList = $items ?? $cartItems ?? collect();
@endphp

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="fw-bold mb-1">
      <img src="{{ Vite::asset('resources/images/svg/cart.svg') }}" alt="Cart" style="width:28px;" class="me-2" />
      Shopping Cart
    </h4>
    <p class="text-muted mb-0">
      You have <strong>{{ $cartList->count() }}</strong> item(s) in your cart
    </p>
  </div>
  <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
    <i class="ti ti-arrow-left me-1"></i> Continue Shopping
  </a>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-4">
    <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show mb-4">
    <i class="ti ti-circle-x me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

@if($cartList->isEmpty())

  {{-- Empty Cart --}}
  <div class="text-center py-5">
    <img src="{{ Vite::asset('resources/images/pages/empty-cart.png') }}"
         alt="Empty Cart" class="empty-cart-img mb-4" />
    <h5 class="fw-bold">Your cart is empty!</h5>
    <p class="text-muted mb-4">Looks like you have not added anything to your cart yet.</p>
    <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
      <i class="ti ti-shopping-bag me-2"></i>Start Shopping
    </a>
  </div>

@else

  <div class="row g-4">

    {{-- Cart Items --}}
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
          @foreach($cartList as $key => $item)
          @php
            $prodId = is_array($item) ? ($item['id'] ?? $key) : ($item->product_id ?? $item->id);
            $prodName = is_array($item) ? ($item['name'] ?? 'Product') : ($item->product->name ?? 'Product');
            $prodPrice = is_array($item) ? ($item['price'] ?? 0) : ($item->product->selling_price ?? $item->price ?? 0);
            $prodQty = is_array($item) ? ($item['qty'] ?? 1) : ($item->quantity ?? 1);
            $rowId = is_array($item) ? ($item['rowId'] ?? $key) : ($item->id ?? $key);
          @endphp
          <div class="d-flex align-items-center gap-3 p-4 {{ !$loop->last ? 'border-bottom' : '' }}">

            {{-- Product Image --}}
            <img src="{{ Vite::asset('resources/images/pages/puma-shoes.jpeg') }}"
                 alt="{{ $prodName }}" class="cart-item-img" />

            {{-- Product Info --}}
            <div class="flex-grow-1">
              <h6 class="mb-1 fw-semibold">
                <a href="{{ route('products.show', $prodId) }}"
                   class="text-body text-decoration-none">
                  {{ $prodName }}
                </a>
              </h6>
            </div>

            {{-- Quantity Controls --}}
            <div class="d-flex align-items-center border rounded px-1">
              <form action="{{ route('cart.update') }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="quantities[{{ $rowId }}]" value="{{ max(1, $prodQty - 1) }}" />
                <button type="submit" class="btn btn-link p-1 text-muted"
                        {{ $prodQty <= 1 ? 'disabled' : '' }}>
                  <i class="ti ti-minus"></i>
                </button>
              </form>
              <span class="qty-control px-2">{{ $prodQty }}</span>
              <form action="{{ route('cart.update') }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="quantities[{{ $rowId }}]" value="{{ $prodQty + 1 }}" />
                <button type="submit" class="btn btn-link p-1 text-muted">
                  <i class="ti ti-plus"></i>
                </button>
              </form>
            </div>

            {{-- Price --}}
            <div class="text-end" style="min-width:80px">
              <div class="fw-bold text-primary">${{ number_format($prodPrice * $prodQty, 2) }}</div>
              <small class="text-muted">${{ number_format($prodPrice, 2) }} each</small>
            </div>

            {{-- Remove --}}
            <form action="{{ route('cart.remove') }}" method="POST">
              @csrf
              <input type="hidden" name="item_id" value="{{ $rowId }}" />
              <button type="submit" class="btn btn-icon btn-sm btn-outline-danger"
                      title="Remove item">
                <i class="ti ti-trash"></i>
              </button>
            </form>
          </div>
          @endforeach
        </div>
      </div>

      {{-- Promo / Trust row --}}
      <div class="row g-3 mt-2">
        <div class="col-sm-4">
          <div class="d-flex align-items-center gap-2 p-3 bg-body-tertiary rounded">
            <img src="{{ Vite::asset('resources/images/svg/rocket.svg') }}" style="width:24px;" alt="Shipping" />
            <div>
              <div class="fw-semibold small">Free Shipping</div>
              <div class="text-muted" style="font-size:.75rem">On orders over $50</div>
            </div>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="d-flex align-items-center gap-2 p-3 bg-body-tertiary rounded">
            <img src="{{ Vite::asset('resources/images/svg/Check.svg') }}" style="width:24px;" alt="Secure" />
            <div>
              <div class="fw-semibold small">Secure Payment</div>
              <div class="text-muted" style="font-size:.75rem">256-bit SSL encryption</div>
            </div>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="d-flex align-items-center gap-2 p-3 bg-body-tertiary rounded">
            <img src="{{ Vite::asset('resources/images/svg/gift.svg') }}" style="width:24px;" alt="Returns" />
            <div>
              <div class="fw-semibold small">Easy Returns</div>
              <div class="text-muted" style="font-size:.75rem">30-day return policy</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Order Summary --}}
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm order-summary-card">
        <div class="card-header border-0 pb-0">
          <h5 class="fw-bold mb-0">Order Summary</h5>
        </div>
        <div class="card-body">
          @php
            $subtotal = $total ?? $cartList->sum(fn($i) => (is_array($i) ? $i['price'] * $i['qty'] : ($i->product->selling_price ?? $i->price) * $i->quantity));
            $shipping = $subtotal >= 50 || $subtotal == 0 ? 0 : 9.99;
            $tax = $subtotal * 0.08;
            $grandTotal = $subtotal + $shipping + $tax;
          @endphp

          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Subtotal ({{ $cartList->count() }} items)</span>
            <span>${{ number_format($subtotal, 2) }}</span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Shipping</span>
            <span class="{{ $shipping == 0 ? 'text-success fw-semibold' : '' }}">
              {{ $shipping == 0 ? 'FREE' : '$' . number_format($shipping, 2) }}
            </span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Estimated Tax (8%)</span>
            <span>${{ number_format($tax, 2) }}</span>
          </div>

          <hr />
          <div class="d-flex justify-content-between mb-4">
            <strong class="fs-5">Total</strong>
            <strong class="fs-5 text-primary">${{ number_format($grandTotal, 2) }}</strong>
          </div>

          <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100 btn-lg">
            <img src="{{ Vite::asset('resources/images/svg/Card.svg') }}" style="width:18px;" alt="" class="me-2" />
            Proceed to Checkout
          </a>

          <div class="mt-3 text-center">
            <div class="d-flex justify-content-center align-items-center gap-2 text-muted small">
              <img src="{{ Vite::asset('resources/images/svg/payment.svg') }}" style="width:16px;" alt="Payment" />
              We accept all major credit cards & PayPal
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endif
@endsection
