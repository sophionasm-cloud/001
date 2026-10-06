@extends('layouts/layoutMaster')

@section('title', 'Order Details')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
  <div class="d-flex align-items-center gap-3">
    <a href="{{ route('vendor.orders.index') }}" class="btn btn-icon btn-outline-secondary">
      <i class="ti ti-arrow-left"></i>
    </a>
    <div>
      <h4 class="fw-bold mb-0">Order Item #{{ $order->id }}</h4>
      <p class="text-muted mb-0 small">Parent Order: #{{ $order->order->id ?? 'N/A' }}</p>
    </div>
  </div>
</div>

<div class="row g-4">
  <div class="col-md-7">
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header border-0">
        <h6 class="fw-bold mb-0">Ordered Product</h6>
      </div>
      <div class="card-body">
        <div class="d-flex align-items-center gap-3 mb-3">
          @if($order->product && $order->product->image)
            <img src="{{ asset('storage/' . $order->product->image) }}" style="width:70px;height:70px;object-fit:cover;border-radius:.5rem;" />
          @else
            <img src="{{ Vite::asset('resources/images/pages/puma-shoes.jpeg') }}" style="width:70px;height:70px;object-fit:cover;border-radius:.5rem;" />
          @endif
          <div>
            <h6 class="fw-bold mb-1">{{ $order->product->name ?? 'Product' }}</h6>
            <div class="text-muted small">Unit Price: ${{ number_format($order->price, 2) }}</div>
            <div class="text-muted small">Quantity: {{ $order->quantity }}</div>
          </div>
          <div class="ms-auto text-end">
            <h5 class="fw-bold text-primary mb-0">${{ number_format($order->price * $order->quantity, 2) }}</h5>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-5">
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header border-0">
        <h6 class="fw-bold mb-0">Customer & Shipping Details</h6>
      </div>
      <div class="card-body">
        <p class="mb-1 fw-semibold">{{ $order->order->user->name ?? 'Customer' }}</p>
        <p class="text-muted small mb-3">{{ $order->order->user->email ?? '' }}</p>

        <h6 class="fw-bold small text-uppercase text-muted">Shipping Address</h6>
        <p class="text-muted small mb-4">{{ $order->order->shipping_address ?? 'Not provided' }}</p>

        <h6 class="fw-bold small text-uppercase text-muted">Fulfillment Status</h6>
        <form action="{{ route('vendor.orders.updateStatus', $order->id) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="d-flex gap-2">
            <select name="status" class="form-select form-select-sm">
              @foreach(['pending','processing','shipped','delivered','cancelled'] as $s)
                <option value="{{ $s }}" {{ ($order->status ?? $order->order->status) == $s ? 'selected' : '' }}>
                  {{ ucfirst($s) }}
                </option>
              @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-primary">Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection
