@extends('layouts/layoutMaster')

@section('title', 'Pending Vendors')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="fw-bold mb-1">Pending Vendor Approvals</h4>
    <p class="text-muted mb-0">Review newly registered vendor applications</p>
  </div>
  <a href="{{ route('admin.vendors.list') }}" class="btn btn-outline-primary btn-sm">
    <i class="ti ti-list me-1"></i>All Vendors
  </a>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-4">
    <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

@if($vendors->isEmpty())
  <div class="card border-0 shadow-sm text-center py-5">
    <div class="card-body">
      <img src="{{ Vite::asset('resources/images/svg/Check.svg') }}"
           alt="All reviewed" style="width:60px;" class="mb-3" />
      <h5 class="fw-bold">No Pending Applications</h5>
      <p class="text-muted mb-3">All vendor accounts have been reviewed.</p>
      <a href="{{ route('admin.vendors.list') }}" class="btn btn-outline-primary">View All Vendors</a>
    </div>
  </div>
@else
  <div class="row g-4">
    @foreach($vendors as $vendor)
    <div class="col-md-6 col-xl-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center gap-3 mb-3">
            @php $vi = ($loop->index % 14) + 1; @endphp
            <img src="{{ Vite::asset('resources/images/avatars/avatar-' . $vi . '.png') }}"
                 onerror="this.src='{{ Vite::asset('resources/images/misc/misc-1.png') }}'"
                 alt="{{ $vendor->store_name ?? $vendor->name }}"
                 style="width:52px;height:52px;border-radius:50%;object-fit:cover;" />
            <div class="flex-grow-1 min-w-0">
              <h6 class="mb-0 fw-bold text-truncate">{{ $vendor->store_name ?? $vendor->name }}</h6>
              <div class="text-muted small text-truncate">{{ $vendor->user->email ?? 'No email' }}</div>
              <span class="badge bg-label-warning mt-1">Pending Approval</span>
            </div>
          </div>

          <p class="text-muted small mb-3">
            {{ $vendor->description ?? 'No store description provided.' }}
          </p>

          <div class="d-flex align-items-center justify-content-between text-muted small mb-3 border-top pt-2">
            <span>Applied:</span>
            <span>{{ $vendor->created_at ? $vendor->created_at->format('M d, Y') : 'Recent' }}</span>
          </div>

          <div class="d-flex gap-2">
            <form action="{{ route('admin.vendors.approve', $vendor->id) }}" method="POST" class="flex-grow-1">
              @csrf
              <button class="btn btn-success btn-sm w-100">
                <i class="ti ti-check me-1"></i>Approve
              </button>
            </form>
            <form action="{{ route('admin.vendors.reject', $vendor->id) }}" method="POST" class="flex-grow-1">
              @csrf
              <button class="btn btn-outline-danger btn-sm w-100">
                <i class="ti ti-x me-1"></i>Reject
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  @if($vendors->hasPages())
    <div class="d-flex justify-content-center mt-4">
      {{ $vendors->links() }}
    </div>
  @endif
@endif

@endsection
