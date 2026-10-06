@extends('layouts/layoutMaster')

@section('title', $product->name)

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
  <div class="d-flex align-items-center gap-3">
    <a href="{{ route('vendor.products.index') }}" class="btn btn-icon btn-outline-secondary">
      <i class="ti ti-arrow-left"></i>
    </a>
    <div>
      <h4 class="fw-bold mb-0">{{ $product->name }}</h4>
      <p class="text-muted mb-0 small">Category: {{ $product->category->name ?? 'Unassigned' }}</p>
    </div>
  </div>
  <a href="{{ route('vendor.products.edit', $product->id) }}" class="btn btn-primary btn-sm">
    <i class="ti ti-edit me-1"></i> Edit Product
  </a>
</div>

<div class="row g-4">
  <div class="col-md-5">
    <div class="card border-0 shadow-sm p-3 text-center">
      @if($product->image)
        <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded" style="max-height: 320px; object-fit: contain;" />
      @else
        <img src="{{ Vite::asset('resources/images/pages/puma-shoes.jpeg') }}" class="img-fluid rounded" style="max-height: 320px; object-fit: contain;" />
      @endif
    </div>
  </div>

  <div class="col-md-7">
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-body">
        <h5 class="fw-bold mb-3">Product Overview</h5>
        <p class="text-muted mb-4">{{ $product->description }}</p>

        <div class="row g-3">
          <div class="col-sm-4">
            <div class="p-3 bg-body-tertiary rounded">
              <small class="text-muted d-block">Cost Price</small>
              <span class="fs-5 fw-bold">${{ number_format($product->cost_price, 2) }}</span>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="p-3 bg-body-tertiary rounded">
              <small class="text-muted d-block">Selling Price</small>
              <span class="fs-5 fw-bold text-primary">${{ number_format($product->selling_price, 2) }}</span>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="p-3 bg-body-tertiary rounded">
              <small class="text-muted d-block">Current Stock</small>
              <span class="fs-5 fw-bold text-success">{{ $product->stock }} units</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
