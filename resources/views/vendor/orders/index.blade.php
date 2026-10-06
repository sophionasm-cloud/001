@extends('layouts/layoutMaster')

@section('title', 'Vendor Orders')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="fw-bold mb-1">Store Orders</h4>
    <p class="text-muted mb-0">Fulfill and manage your customers' product orders</p>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-4">
    <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead class="bg-body-tertiary">
        <tr>
          <th>Order Item</th>
          <th>Product</th>
          <th>Customer</th>
          <th>Price & Qty</th>
          <th>Status</th>
          <th>Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($orderItems as $item)
        <tr>
          <td>
            <span class="fw-bold text-primary">#{{ $item->order->id ?? $item->id }}</span>
          </td>
          <td>
            <div class="d-flex align-items-center gap-2">
              @if($item->product && $item->product->image)
                <img src="{{ asset('storage/' . $item->product->image) }}" style="width:36px;height:36px;object-fit:cover;border-radius:.375rem;" />
              @else
                <img src="{{ Vite::asset('resources/images/pages/puma-shoes.jpeg') }}" style="width:36px;height:36px;object-fit:cover;border-radius:.375rem;" />
              @endif
              <div>
                <span class="fw-semibold small">{{ $item->product->name ?? 'Product' }}</span>
              </div>
            </div>
          </td>
          <td>
            <div class="small fw-semibold">{{ $item->order->user->name ?? 'Customer' }}</div>
            <div class="text-muted" style="font-size: .75rem;">{{ $item->order->shipping_address ?? '' }}</div>
          </td>
          <td>
            <div class="fw-bold">${{ number_format($item->price, 2) }}</div>
            <div class="text-muted small">Qty: {{ $item->quantity }}</div>
          </td>
          <td>
            @php
              $status = $item->status ?? $item->order->status ?? 'pending';
              $badges = ['pending'=>'warning','processing'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger'];
            @endphp
            <form action="{{ route('vendor.orders.updateStatus', $item->id) }}" method="POST">
              @csrf
              @method('PUT')
              <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="width:130px;">
                @foreach(['pending','processing','shipped','delivered','cancelled'] as $s)
                  <option value="{{ $s }}" {{ $status == $s ? 'selected' : '' }}>
                    {{ ucfirst($s) }}
                  </option>
                @endforeach
              </select>
            </form>
          </td>
          <td class="text-muted small">{{ $item->created_at ? $item->created_at->format('M d, Y') : 'Recent' }}</td>
          <td>
            <a href="{{ route('vendor.orders.show', $item->id) }}" class="btn btn-sm btn-icon btn-outline-primary">
              <i class="ti ti-eye"></i>
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="text-center py-5 text-muted">
            <img src="{{ Vite::asset('resources/images/pages/empty-cart.png') }}" style="width:90px;opacity:.5;" class="mb-2 d-block mx-auto" />
            No incoming orders yet
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($orderItems->hasPages())
    <div class="card-footer border-0">
      <div class="d-flex justify-content-center">
        {{ $orderItems->links() }}
      </div>
    </div>
  @endif
</div>

@endsection
