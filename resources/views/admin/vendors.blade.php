@extends('layouts/layoutMaster')

@section('title', 'Vendor Management')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="fw-bold mb-1">Vendor Management</h4>
    <p class="text-muted mb-0">All registered vendors on your platform</p>
  </div>
  <div class="d-flex gap-2">
    @if(isset($showPending) && $showPending)
      <a href="{{ route('admin.vendors.list') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-list me-1"></i>All Vendors
      </a>
    @else
      <a href="{{ route('admin.vendors.pending') }}" class="btn btn-warning btn-sm">
        <i class="ti ti-clock me-1"></i>
        Pending ({{ $pendingCount ?? 0 }})
      </a>
    @endif
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-4">
    <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show mb-4">
    <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

{{-- Filter/Search Bar --}}
<div class="card border-0 shadow-sm mb-4">
  <div class="card-body">
    <form method="GET" action="{{ request()->url() }}" class="row g-3 align-items-end">
      <div class="col-sm-5">
        <label class="form-label">Search</label>
        <div class="input-group">
          <span class="input-group-text"><i class="ti ti-search"></i></span>
          <input type="text" name="q" class="form-control" placeholder="Vendor name or email…"
                 value="{{ request('q') }}" />
        </div>
      </div>
      <div class="col-sm-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <option value="">All Status</option>
          <option value="pending"   {{ request('status') == 'pending'   ? 'selected' : '' }}>Pending</option>
          <option value="approved"  {{ request('status') == 'approved'  ? 'selected' : '' }}>Approved</option>
          <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
          <option value="rejected"  {{ request('status') == 'rejected'  ? 'selected' : '' }}>Rejected</option>
        </select>
      </div>
      <div class="col-sm-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary flex-grow-1">
          <i class="ti ti-filter me-1"></i>Filter
        </button>
        <a href="{{ request()->url() }}" class="btn btn-outline-secondary">Reset</a>
      </div>
    </form>
  </div>
</div>

{{-- Vendors Grid/List --}}
@if($vendors->isEmpty())
  <div class="text-center py-5">
    <img src="{{ Vite::asset('resources/images/pages/misc-under-maintenance.png') }}"
         alt="No vendors" style="max-width:200px;opacity:.7;" class="mb-3" />
    <h5 class="text-muted">No vendors found</h5>
    <p class="text-muted">{{ isset($showPending) && $showPending ? 'No pending vendor applications.' : 'No vendors match your filters.' }}</p>
  </div>
@else
  <div class="row g-4">
    @foreach($vendors as $vendor)
    <div class="col-sm-6 col-xl-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">

          {{-- Vendor header --}}
          <div class="d-flex align-items-start gap-3 mb-3">
            @php $vi = ($loop->index % 14) + 1; @endphp
            @if($vendor->logo ?? false)
              <img src="{{ asset('storage/' . $vendor->logo) }}"
                   alt="{{ $vendor->name }}"
                   style="width:56px;height:56px;border-radius:.5rem;object-fit:cover;" />
            @else
              <img src="{{ Vite::asset('resources/images/avatars/avatar-' . $vi . '.png') }}"
                   onerror="this.src='{{ Vite::asset('resources/images/misc/misc-1.png') }}'"
                   alt="{{ $vendor->name }}"
                   style="width:56px;height:56px;border-radius:.5rem;object-fit:cover;" />
            @endif
            <div class="flex-grow-1 min-w-0">
              <h6 class="mb-0 fw-bold text-truncate">{{ $vendor->name }}</h6>
              <div class="text-muted small text-truncate">{{ $vendor->email ?? $vendor->user->email }}</div>
              <div class="mt-1">
                @php
                  $statusColors = ['pending'=>'warning','approved'=>'success','suspended'=>'danger','rejected'=>'secondary'];
                  $sc = $statusColors[$vendor->status ?? 'pending'] ?? 'secondary';
                @endphp
                <span class="badge bg-label-{{ $sc }}">{{ ucfirst($vendor->status ?? 'pending') }}</span>
              </div>
            </div>
          </div>

          {{-- Vendor stats --}}
          <div class="row g-2 mb-3 text-center">
            <div class="col-4">
              <div class="bg-body-tertiary rounded p-2">
                <div class="fw-bold">{{ $vendor->products_count ?? 0 }}</div>
                <div class="text-muted" style="font-size:.7rem">Products</div>
              </div>
            </div>
            <div class="col-4">
              <div class="bg-body-tertiary rounded p-2">
                <div class="fw-bold">{{ $vendor->orders_count ?? 0 }}</div>
                <div class="text-muted" style="font-size:.7rem">Orders</div>
              </div>
            </div>
            <div class="col-4">
              <div class="bg-body-tertiary rounded p-2">
                <div class="fw-bold">${{ number_format($vendor->total_revenue ?? 0, 0) }}</div>
                <div class="text-muted" style="font-size:.7rem">Revenue</div>
              </div>
            </div>
          </div>

          <div class="text-muted small mb-3">
            <i class="ti ti-calendar me-1"></i>
            Joined {{ ($vendor->created_at ?? now())->format('M d, Y') }}
          </div>

          {{-- Actions --}}
          <div class="d-flex gap-2">
            @if(($vendor->status ?? 'pending') === 'pending')
              <form action="{{ route('admin.vendors.approve', $vendor) }}" method="POST" class="flex-grow-1">
                @csrf
                <button class="btn btn-success btn-sm w-100">
                  <i class="ti ti-check me-1"></i>Approve
                </button>
              </form>
              <form action="{{ route('admin.vendors.reject', $vendor) }}" method="POST" class="flex-grow-1">
                @csrf
                <button class="btn btn-outline-danger btn-sm w-100">
                  <i class="ti ti-x me-1"></i>Reject
                </button>
              </form>
            @elseif(($vendor->status ?? '') === 'approved')
              <form action="{{ route('admin.vendors.suspend', $vendor) }}" method="POST" class="flex-grow-1">
                @csrf
                <button class="btn btn-warning btn-sm w-100"
                        onclick="return confirm('Suspend {{ addslashes($vendor->name) }}?')">
                  <i class="ti ti-ban me-1"></i>Suspend
                </button>
              </form>
              <button class="btn btn-outline-primary btn-sm flex-grow-1">
                <i class="ti ti-eye me-1"></i>View
              </button>
            @else
              <form action="{{ route('admin.vendors.approve', $vendor) }}" method="POST" class="flex-grow-1">
                @csrf
                <button class="btn btn-outline-success btn-sm w-100">
                  <i class="ti ti-refresh me-1"></i>Re-activate
                </button>
              </form>
            @endif
          </div>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  {{-- Pagination --}}
  @if($vendors->hasPages())
  <div class="d-flex justify-content-center mt-5">
    {{ $vendors->withQueryString()->links() }}
  </div>
  @endif
@endif

@endsection
