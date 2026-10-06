@extends('layouts/layoutMaster')

@section('title', 'Customer Dashboard')

@section('content')

{{-- Welcome Banner --}}
<div class="card border-0 shadow-sm mb-4 overflow-hidden">
  <div class="card-body p-0 position-relative">
    <img src="{{ Vite::asset('resources/images/pages/user-profile-header-bg.png') }}"
         alt="Profile background"
         style="width:100%;height:120px;object-fit:cover;" />
    <div class="p-4 d-flex align-items-end gap-4" style="margin-top:-40px;">
      @if($user->profile_photo_path ?? false)
        <img src="{{ asset('storage/' . $user->profile_photo_path) }}"
             alt="{{ $user->name }}"
             style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:4px solid white;box-shadow:0 4px 12px rgba(0,0,0,.15);" />
      @else
        <img src="{{ Vite::asset('resources/images/avatars/avatar-1.png') }}"
             onerror="this.src='{{ Vite::asset('resources/images/misc/misc-1.png') }}'"
             alt="{{ $user->name }}"
             style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:4px solid white;box-shadow:0 4px 12px rgba(0,0,0,.15);" />
      @endif
      <div class="pb-2">
        <h5 class="mb-0 fw-bold">{{ $user->name }}</h5>
        <p class="text-muted mb-0 small">{{ $user->email }}</p>
      </div>
      <div class="ms-auto pb-2 d-flex gap-2">
        <a href="{{ url('/profile') }}" class="btn btn-sm btn-outline-primary">
          <i class="ti ti-edit me-1"></i>Edit Profile
        </a>
      </div>
    </div>
  </div>
</div>

{{-- Quick Stats --}}
<div class="row g-4 mb-4">
  <div class="col-sm-6 col-lg-3">
    <div class="card border-0 shadow-sm text-center p-4">
      <div class="avatar avatar-lg bg-label-primary rounded mx-auto mb-3">
        <i class="ti ti-shopping-bag ti-lg text-primary"></i>
      </div>
      <h4 class="fw-bold mb-0">{{ $orders->total() ?? $orders->count() }}</h4>
      <div class="text-muted small">Total Orders</div>
    </div>
  </div>
  <div class="col-sm-6 col-lg-3">
    <div class="card border-0 shadow-sm text-center p-4">
      <div class="avatar avatar-lg bg-label-success rounded mx-auto mb-3">
        <img src="{{ Vite::asset('resources/images/svg/cart.svg') }}"
             style="width:24px;" alt="Cart" />
      </div>
      <h4 class="fw-bold mb-0">{{ $cart->items->count() ?? 0 }}</h4>
      <div class="text-muted small">Items in Cart</div>
    </div>
  </div>
  <div class="col-sm-6 col-lg-3">
    <div class="card border-0 shadow-sm text-center p-4">
      <div class="avatar avatar-lg bg-label-warning rounded mx-auto mb-3">
        <i class="ti ti-star ti-lg text-warning"></i>
      </div>
      <h4 class="fw-bold mb-0">{{ $user->reviews_count ?? 0 }}</h4>
      <div class="text-muted small">Reviews Written</div>
    </div>
  </div>
  <div class="col-sm-6 col-lg-3">
    <div class="card border-0 shadow-sm text-center p-4">
      <div class="avatar avatar-lg bg-label-info rounded mx-auto mb-3">
        <img src="{{ Vite::asset('resources/images/svg/Wallet.svg') }}"
             style="width:24px;" alt="Wallet" />
      </div>
      <h4 class="fw-bold mb-0">
        ${{ number_format($user->total_spent ?? 0, 0) }}
      </h4>
      <div class="text-muted small">Total Spent</div>
    </div>
  </div>
</div>

<div class="row g-4">

  {{-- Recent Orders --}}
  <div class="col-lg-8">
    <div class="card border-0 shadow-sm">
      <div class="card-header border-0 d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0">Recent Orders</h6>
        <a href="{{ url('/orders') }}" class="btn btn-sm btn-outline-primary">View All</a>
      </div>
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="bg-body-tertiary">
            <tr>
              <th>Order</th>
              <th>Items</th>
              <th>Total</th>
              <th>Status</th>
              <th>Date</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @forelse($orders as $order)
            <tr>
              <td><span class="fw-semibold text-primary">#{{ $order->id }}</span></td>
              <td class="text-muted">{{ $order->items->count() ?? 1 }} item(s)</td>
              <td class="fw-semibold">${{ number_format($order->total_amount, 2) }}</td>
              <td>
                @php
                  $sc = ['pending'=>'warning','processing'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger'];
                  $c = $sc[$order->status] ?? 'secondary';
                @endphp
                <span class="badge bg-label-{{ $c }}">{{ ucfirst($order->status) }}</span>
              </td>
              <td class="text-muted small">{{ $order->created_at->format('M d, Y') }}</td>
              <td>
                <a href="{{ url('/orders/' . $order->id) }}" class="btn btn-icon btn-sm btn-outline-secondary">
                  <i class="ti ti-eye"></i>
                </a>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="text-center py-4">
                <img src="{{ Vite::asset('resources/images/pages/empty-cart.png') }}"
                     style="width:100px;opacity:.6;" alt="No orders" class="mb-2 d-block mx-auto" />
                <div class="text-muted">No orders yet</div>
                <a href="{{ route('products.index') }}" class="btn btn-sm btn-primary mt-2">Start Shopping</a>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Sidebar: Cart + Quick Links --}}
  <div class="col-lg-4">

    {{-- Cart preview --}}
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header border-0 d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0">
          <img src="{{ Vite::asset('resources/images/svg/cart.svg') }}" style="width:18px;" class="me-2" />
          My Cart
        </h6>
        @if($cart && $cart->items->count())
          <span class="badge bg-primary">{{ $cart->items->count() }}</span>
        @endif
      </div>
      <div class="card-body p-0">
        @if($cart && $cart->items->count())
          @foreach($cart->items->take(3) as $item)
          <div class="d-flex align-items-center gap-3 p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
            @if($item->product->images ?? false)
              <img src="{{ asset('storage/' . $item->product->images[0]) }}"
                   alt="{{ $item->product->name }}"
                   style="width:44px;height:44px;border-radius:.375rem;object-fit:cover;" />
            @else
              <img src="{{ Vite::asset('resources/images/pages/puma-shoes.jpeg') }}"
                   alt="{{ $item->product->name }}"
                   style="width:44px;height:44px;border-radius:.375rem;object-fit:cover;" />
            @endif
            <div class="flex-grow-1 min-w-0">
              <div class="fw-semibold small text-truncate">{{ $item->product->name }}</div>
              <div class="text-muted" style="font-size:.75rem">Qty: {{ $item->quantity }}</div>
            </div>
            <div class="fw-semibold small">${{ number_format($item->product->price * $item->quantity, 2) }}</div>
          </div>
          @endforeach
          @if($cart->items->count() > 3)
          <div class="text-center p-2 border-top">
            <small class="text-muted">+{{ $cart->items->count() - 3 }} more items</small>
          </div>
          @endif
          <div class="p-3 border-top">
            <a href="{{ route('cart.view') }}" class="btn btn-primary w-100 btn-sm">
              View Cart & Checkout
            </a>
          </div>
        @else
          <div class="text-center py-4">
            <img src="{{ Vite::asset('resources/images/pages/empty-cart.png') }}"
                 style="width:80px;opacity:.5;" alt="Empty cart" class="mb-2" />
            <div class="text-muted small">Your cart is empty</div>
            <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-primary mt-2">
              Shop Now
            </a>
          </div>
        @endif
      </div>
    </div>

    {{-- Quick links --}}
    <div class="card border-0 shadow-sm">
      <div class="card-header border-0">
        <h6 class="fw-bold mb-0">Quick Links</h6>
      </div>
      <div class="list-group list-group-flush">
        <a href="{{ url('/profile') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-3">
          <img src="{{ Vite::asset('resources/images/svg/user.svg') }}" style="width:20px;" alt="" />
          <span>My Profile</span>
          <i class="ti ti-chevron-right ms-auto text-muted"></i>
        </a>
        <a href="{{ url('/orders') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-3">
          <img src="{{ Vite::asset('resources/images/svg/paper-send.svg') }}" style="width:20px;" alt="" />
          <span>Order History</span>
          <i class="ti ti-chevron-right ms-auto text-muted"></i>
        </a>
        <a href="{{ url('/addresses') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-3">
          <img src="{{ Vite::asset('resources/images/svg/address.svg') }}" style="width:20px;" alt="" />
          <span>Saved Addresses</span>
          <i class="ti ti-chevron-right ms-auto text-muted"></i>
        </a>
        <a href="{{ url('/wishlist') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-3">
          <img src="{{ Vite::asset('resources/images/svg/gift.svg') }}" style="width:20px;" alt="" />
          <span>Wishlist</span>
          <i class="ti ti-chevron-right ms-auto text-muted"></i>
        </a>
        <a href="{{ route('logout') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-3 text-danger"
           onclick="event.preventDefault();document.getElementById('customer-logout').submit();">
          <i class="ti ti-logout text-danger" style="width:20px;text-align:center;"></i>
          <span>Logout</span>
        </a>
        <form id="customer-logout" method="POST" action="{{ route('logout') }}">@csrf</form>
      </div>
    </div>

  </div>
</div>

@endsection
