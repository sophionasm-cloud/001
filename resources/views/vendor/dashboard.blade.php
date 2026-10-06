@extends('layouts/layoutMaster')

@section('title', 'Vendor Dashboard')

@section('content')

{{-- Header Banner --}}
<div class="card border-0 shadow-sm mb-4 overflow-hidden">
  <div class="card-body p-0 position-relative">
    <img src="{{ Vite::asset('resources/images/pages/user-profile-header-bg.png') }}"
         alt="Store Banner"
         style="width:100%;height:130px;object-fit:cover;" />
    <div class="p-4 d-flex align-items-end gap-4" style="margin-top:-45px;">
      <img src="{{ Vite::asset('resources/images/avatars/avatar-1.png') }}"
           onerror="this.src='{{ Vite::asset('resources/images/misc/misc-1.png') }}'"
           alt="{{ $vendor->store_name ?? 'Vendor' }}"
           style="width:90px;height:90px;border-radius:.75rem;object-fit:cover;border:4px solid white;box-shadow:0 4px 16px rgba(0,0,0,.15);" />
      <div class="pb-2">
        <div class="d-flex align-items-center gap-2">
          <h5 class="mb-0 fw-bold">{{ $vendor->store_name ?? 'My Store' }}</h5>
          <span class="badge bg-label-success">
            {{ ucfirst($vendor->approval_status ?? 'Active') }}
          </span>
        </div>
        <p class="text-muted mb-0 small">{{ $vendor->description ?? 'Welcome to your vendor store dashboard' }}</p>
      </div>
      <div class="ms-auto pb-2 d-flex gap-2">
        <a href="{{ route('vendor.products.create') }}" class="btn btn-primary btn-sm">
          <i class="ti ti-plus me-1"></i>Add Product
        </a>
        <a href="{{ route('vendor.orders.index') }}" class="btn btn-outline-secondary btn-sm">
          <i class="ti ti-shopping-bag me-1"></i>Orders
        </a>
      </div>
    </div>
  </div>
</div>

{{-- Stats Row --}}
<div class="row g-4 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-lg bg-label-primary rounded">
          <img src="{{ Vite::asset('resources/images/svg/lightbulb.svg') }}" style="width:24px;" alt="Products" />
        </div>
        <div>
          <div class="text-muted small">Total Products</div>
          <h4 class="mb-0 fw-bold">{{ $vendor->products->count() }}</h4>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-lg bg-label-success rounded">
          <img src="{{ Vite::asset('resources/images/svg/paper-send.svg') }}" style="width:24px;" alt="Sales" />
        </div>
        <div>
          <div class="text-muted small">Items Sold</div>
          <h4 class="mb-0 fw-bold">{{ $totalSales ?? 0 }}</h4>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-lg bg-label-warning rounded">
          <img src="{{ Vite::asset('resources/images/svg/Wallet.svg') }}" style="width:24px;" alt="Revenue" />
        </div>
        <div>
          <div class="text-muted small">Total Revenue</div>
          <h4 class="mb-0 fw-bold">${{ number_format($totalRevenue ?? 0, 2) }}</h4>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-lg bg-label-danger rounded">
          <i class="ti ti-alert-triangle text-danger ti-28px"></i>
        </div>
        <div>
          <div class="text-muted small">Low Stock Items</div>
          <h4 class="mb-0 fw-bold">{{ $lowStockProducts ? $lowStockProducts->count() : 0 }}</h4>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4">

  {{-- Recent Orders & Low Stock --}}
  <div class="col-xl-8">
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header border-0 d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0">Low Stock Products (< 10 left)</h6>
        <a href="{{ route('vendor.products.index') }}" class="btn btn-sm btn-outline-primary">View Products</a>
      </div>
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="bg-body-tertiary">
            <tr>
              <th>Product</th>
              <th>Price</th>
              <th>Stock</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($lowStockProducts ?? [] as $product)
            <tr>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <span class="fw-semibold">{{ $product->name }}</span>
                </div>
              </td>
              <td class="fw-semibold">${{ number_format($product->selling_price, 2) }}</td>
              <td><span class="badge bg-label-danger">{{ $product->stock }} remaining</span></td>
              <td>
                <span class="badge bg-label-{{ $product->is_active ? 'success' : 'secondary' }}">
                  {{ $product->is_active ? 'Active' : 'Draft' }}
                </span>
              </td>
              <td>
                <a href="{{ route('vendor.products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary">
                  Update Stock
                </a>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="text-center py-4 text-muted">
                <img src="{{ Vite::asset('resources/images/svg/Check.svg') }}" style="width:36px;" class="mb-2 d-block mx-auto" />
                All products have healthy inventory levels!
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Quick Actions --}}
  <div class="col-xl-4">
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header border-0">
        <h6 class="fw-bold mb-0">Store Navigation</h6>
      </div>
      <div class="list-group list-group-flush">
        <a href="{{ route('vendor.products.create') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-3">
          <i class="ti ti-plus text-primary"></i>
          <span>Add New Product</span>
        </a>
        <a href="{{ route('vendor.products.index') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-3">
          <i class="ti ti-box text-info"></i>
          <span>Manage Catalog</span>
        </a>
        <a href="{{ route('vendor.orders.index') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-3">
          <i class="ti ti-shopping-cart text-success"></i>
          <span>Manage Orders</span>
        </a>
      </div>
    </div>
  </div>

</div>

@endsection
