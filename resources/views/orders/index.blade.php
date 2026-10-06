@extends('layouts/layoutMaster')

@section('title', 'My Orders')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="fw-bold mb-1">My Orders</h4>
    <p class="text-muted mb-0">Track and manage all your orders</p>
  </div>
  <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm">
    <i class="ti ti-plus me-1"></i>Continue Shopping
  </a>
</div>

{{-- Filter tabs --}}
<div class="card border-0 shadow-sm mb-4">
  <div class="card-body p-0">
    <ul class="nav nav-tabs border-0 px-3">
      @php $statuses = ['all'=>'All','pending'=>'Pending','processing'=>'Processing','shipped'=>'Shipped','delivered'=>'Delivered','cancelled'=>'Cancelled']; @endphp
      @foreach($statuses as $key => $label)
      <li class="nav-item">
        <a class="nav-link {{ request('status', 'all') == $key ? 'active' : '' }}"
           href="?status={{ $key }}">
          {{ $label }}
          @if($key !== 'all')
            <span class="badge bg-label-secondary ms-1">
              {{ $orders->where('status', $key)->count() ?? 0 }}
            </span>
          @endif
        </a>
      </li>
      @endforeach
    </ul>
  </div>
</div>

{{-- Orders list --}}
@if($orders->isEmpty())
  <div class="text-center py-5">
    <img src="{{ Vite::asset('resources/images/pages/empty-cart.png') }}"
         alt="No orders" style="max-width:180px;opacity:.7;" class="mb-3" />
    <h5 class="text-muted">No orders found</h5>
    <p class="text-muted">{{ request('status') && request('status') !== 'all' ? 'No ' . request('status') . ' orders.' : 'You haven\'t placed any orders yet.' }}</p>
    <a href="{{ route('products.index') }}" class="btn btn-primary">
      <i class="ti ti-shopping-bag me-2"></i>Browse Products
    </a>
  </div>
@else
  <div class="d-flex flex-column gap-4">
    @foreach($orders as $order)
    <div class="card border-0 shadow-sm">

      {{-- Order Header --}}
      <div class="card-header bg-body-tertiary d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
          <div>
            <span class="fw-bold text-primary">Order #{{ $order->id }}</span>
            <span class="text-muted small ms-2">{{ $order->created_at->format('M d, Y') }}</span>
          </div>
          @php $sc = ['pending'=>'warning','processing'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger']; @endphp
          <span class="badge bg-label-{{ $sc[$order->status] ?? 'secondary' }}">
            {{ ucfirst($order->status) }}
          </span>
        </div>
        <div class="d-flex align-items-center gap-3">
          <div class="text-end">
            <div class="text-muted small">Total</div>
            <div class="fw-bold text-primary">${{ number_format($order->total_amount, 2) }}</div>
          </div>
          <a href="{{ url('/orders/' . $order->id) }}" class="btn btn-sm btn-outline-primary">
            <i class="ti ti-eye me-1"></i>Details
          </a>
          @if($order->status === 'delivered')
          <button class="btn btn-sm btn-outline-success">
            <i class="ti ti-star me-1"></i>Review
          </button>
          @endif
        </div>
      </div>

      {{-- Order Items --}}
      <div class="card-body p-0">
        @foreach($order->items->take(3) as $item)
        <div class="d-flex align-items-center gap-3 p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
          @if($item->product && $item->product->images && count($item->product->images))
            <img src="{{ asset('storage/' . $item->product->images[0]) }}"
                 alt="{{ $item->product->name ?? 'Product' }}"
                 style="width:60px;height:60px;border-radius:.5rem;object-fit:cover;" />
          @else
            <img src="{{ Vite::asset('resources/images/pages/puma-shoes.jpeg') }}"
                 alt="Product"
                 style="width:60px;height:60px;border-radius:.5rem;object-fit:cover;" />
          @endif
          <div class="flex-grow-1">
            <div class="fw-semibold">{{ $item->product->name ?? 'Product (deleted)' }}</div>
            <div class="text-muted small">
              Qty: {{ $item->quantity }}
              @if($item->product->vendor ?? false)
                · by {{ $item->product->vendor->name }}
              @endif
            </div>
          </div>
          <div class="fw-semibold">${{ number_format($item->price * $item->quantity, 2) }}</div>
        </div>
        @endforeach
        @if($order->items->count() > 3)
        <div class="text-center p-2 bg-body-tertiary border-top">
          <a href="{{ url('/orders/' . $order->id) }}" class="text-muted small">
            +{{ $order->items->count() - 3 }} more items — View All
          </a>
        </div>
        @endif
      </div>

      {{-- Shipping progress (for active orders) --}}
      @if(in_array($order->status, ['processing','shipped']))
      <div class="card-footer border-0 bg-transparent">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <small class="text-muted">Delivery Progress</small>
          @if($order->tracking_number ?? false)
          <small class="text-primary">Tracking: {{ $order->tracking_number }}</small>
          @endif
        </div>
        <div class="d-flex justify-content-between position-relative">
          @php
            $steps = ['Ordered','Processing','Shipped','Delivered'];
            $currentStep = match($order->status) {
              'pending' => 0, 'processing' => 1, 'shipped' => 2, 'delivered' => 3, default => 0
            };
          @endphp
          <div class="position-absolute" style="top:12px;left:10%;right:10%;height:2px;background:var(--bs-border-color);z-index:0;"></div>
          @foreach($steps as $si => $step)
          <div class="text-center position-relative" style="z-index:1;">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center"
                 style="width:24px;height:24px;background:{{ $si <= $currentStep ? 'var(--bs-primary)' : 'var(--bs-border-color)' }};">
              <i class="ti ti-check" style="font-size:.65rem;color:white;"></i>
            </div>
            <div class="small mt-1 text-muted" style="font-size:.7rem;">{{ $step }}</div>
          </div>
          @endforeach
        </div>
      </div>
      @endif

    </div>
    @endforeach
  </div>

  {{-- Pagination --}}
  @if($orders->hasPages())
  <div class="d-flex justify-content-center mt-4">
    {{ $orders->withQueryString()->links() }}
  </div>
  @endif
@endif

@endsection
