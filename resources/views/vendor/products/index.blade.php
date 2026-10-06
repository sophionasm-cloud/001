@extends('layouts/layoutMaster')

@section('title', 'Manage Products')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="fw-bold mb-1">My Products</h4>
    <p class="text-muted mb-0">Manage your product catalog</p>
  </div>
  <a href="{{ route('vendor.products.create') }}" class="btn btn-primary">
    <i class="ti ti-plus me-1"></i>Add New Product
  </a>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-4">
    <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

{{-- Search & Filter --}}
<div class="card border-0 shadow-sm mb-4">
  <div class="card-body">
    <form method="GET" class="row g-3 align-items-end">
      <div class="col-sm-5">
        <div class="input-group">
          <span class="input-group-text"><i class="ti ti-search"></i></span>
          <input type="text" name="q" class="form-control" placeholder="Search products…"
                 value="{{ request('q') }}" />
        </div>
      </div>
      <div class="col-sm-3">
        <select name="status" class="form-select">
          <option value="">All Status</option>
          <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
          <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
        </select>
      </div>
      <div class="col-sm-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
        <a href="{{ route('vendor.products.index') }}" class="btn btn-outline-secondary">Reset</a>
      </div>
    </form>
  </div>
</div>

{{-- Products Grid --}}
@if($products->isEmpty())
  <div class="text-center py-5">
    <img src="{{ Vite::asset('resources/images/svg/lightbulb.svg') }}"
         alt="No products" style="width:80px;opacity:.4;" class="mb-3" />
    <h5 class="text-muted">No products yet</h5>
    <p class="text-muted">Start building your catalog by adding your first product.</p>
    <a href="{{ route('vendor.products.create') }}" class="btn btn-primary">
      <i class="ti ti-plus me-2"></i>Add First Product
    </a>
  </div>
@else
  <div class="row g-4">
    @foreach($products as $product)
    <div class="col-sm-6 col-lg-4 col-xl-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="position-relative">
          @if($product->images && count($product->images))
            <img src="{{ asset('storage/' . $product->images[0]) }}"
                 alt="{{ $product->name }}"
                 class="card-img-top" style="height:180px;object-fit:cover;" />
          @else
            <img src="{{ Vite::asset('resources/images/pages/puma-shoes.jpeg') }}"
                 alt="{{ $product->name }}"
                 class="card-img-top" style="height:180px;object-fit:cover;" />
          @endif
          <div class="position-absolute top-0 end-0 m-2 d-flex gap-1">
            <span class="badge bg-{{ ($product->is_active ?? true) ? 'success' : 'secondary' }}">
              {{ ($product->is_active ?? true) ? 'Active' : 'Inactive' }}
            </span>
          </div>
          @if(($product->stock_quantity ?? 10) <= 5)
            <div class="position-absolute top-0 start-0 m-2">
              <span class="badge bg-danger">Low Stock</span>
            </div>
          @endif
        </div>
        <div class="card-body d-flex flex-column">
          <h6 class="fw-semibold mb-1">{{ Str::limit($product->name, 35) }}</h6>
          <p class="text-muted small mb-2 flex-grow-1">{{ Str::limit($product->description, 60) }}</p>
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-bold text-primary">${{ number_format($product->selling_price ?? $product->price ?? 0, 2) }}</span>
            <span class="text-muted small">Stock: {{ $product->stock ?? $product->stock_quantity ?? 0 }}</span>
          </div>
          <div class="d-flex gap-2">
            <a href="{{ route('vendor.products.edit', $product->id) }}"
               class="btn btn-outline-primary btn-sm flex-grow-1">
              <i class="ti ti-edit me-1"></i>Edit
            </a>
            <form action="{{ route('vendor.products.destroy', $product) }}" method="POST"
                  onsubmit="return confirm('Delete {{ addslashes($product->name) }}?')">
              @csrf @method('DELETE')
              <button class="btn btn-outline-danger btn-sm">
                <i class="ti ti-trash"></i>
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  <div class="d-flex justify-content-center mt-4">
    {{ $products->withQueryString()->links() }}
  </div>
@endif

@endsection
