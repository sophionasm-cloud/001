@extends('layouts/layoutMaster')

@section('title', 'Vendors List')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="fw-bold mb-1">Vendors Directory</h4>
    <p class="text-muted mb-0">Overview of all active, suspended, and registered vendors</p>
  </div>
  <a href="{{ route('admin.vendors.pending') }}" class="btn btn-warning btn-sm">
    <i class="ti ti-clock me-1"></i>Pending Approvals
  </a>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-4">
    <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="bg-body-tertiary">
        <tr>
          <th>Store Name</th>
          <th>Owner Email</th>
          <th>Status</th>
          <th>Joined Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($vendors as $vendor)
        <tr>
          <td>
            <div class="d-flex align-items-center gap-3">
              @php $vi = ($loop->index % 14) + 1; @endphp
              <img src="{{ Vite::asset('resources/images/avatars/avatar-' . $vi . '.png') }}"
                   onerror="this.src='{{ Vite::asset('resources/images/misc/misc-1.png') }}'"
                   alt="{{ $vendor->store_name ?? $vendor->name }}"
                   style="width:36px;height:36px;border-radius:50%;object-fit:cover;" />
              <div>
                <span class="fw-semibold">{{ $vendor->store_name ?? $vendor->name ?? 'Vendor Store' }}</span>
                <div class="text-muted small">ID: #{{ $vendor->id }}</div>
              </div>
            </div>
          </td>
          <td>{{ $vendor->user->email ?? 'N/A' }}</td>
          <td>
            @php
              $status = $vendor->approval_status ?? $vendor->status ?? 'pending';
              $badges = ['active' => 'success', 'approved' => 'success', 'pending' => 'warning', 'suspended' => 'danger', 'rejected' => 'secondary'];
            @endphp
            <span class="badge bg-label-{{ $badges[$status] ?? 'secondary' }}">
              {{ ucfirst($status) }}
            </span>
          </td>
          <td class="text-muted small">{{ $vendor->created_at ? $vendor->created_at->format('M d, Y') : 'N/A' }}</td>
          <td>
            <div class="d-flex gap-2">
              @if(($vendor->approval_status ?? '') !== 'active')
                <form action="{{ route('admin.vendors.approve', $vendor->id) }}" method="POST">
                  @csrf
                  <button class="btn btn-sm btn-outline-success" title="Approve">
                    <i class="ti ti-check"></i> Approve
                  </button>
                </form>
              @endif
              @if(($vendor->approval_status ?? '') === 'active')
                <form action="{{ route('admin.vendors.suspend', $vendor->id) }}" method="POST">
                  @csrf
                  <button class="btn btn-sm btn-outline-warning" title="Suspend">
                    <i class="ti ti-ban"></i> Suspend
                  </button>
                </form>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center py-5 text-muted">
            <img src="{{ Vite::asset('resources/images/svg/paper-send.svg') }}" style="width:40px;opacity:.5;" class="mb-2 d-block mx-auto" />
            No vendors found in directory
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($vendors->hasPages())
    <div class="card-footer border-0">
      <div class="d-flex justify-content-center">
        {{ $vendors->links() }}
      </div>
    </div>
  @endif
</div>

@endsection
