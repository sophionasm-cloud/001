@extends('layouts/layoutMaster')

@section('title', 'User Management')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="fw-bold mb-1">User Management</h4>
    <p class="text-muted mb-0">{{ $users->total() }} registered users on the platform</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ url('/admin/users/export') }}" class="btn btn-outline-secondary btn-sm">
      <i class="ti ti-download me-1"></i>Export
    </a>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-add-user">
      <i class="ti ti-plus me-1"></i>Add User
    </button>
  </div>
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
          <input type="text" name="q" class="form-control" placeholder="Search users…"
                 value="{{ request('q') }}" />
        </div>
      </div>
      <div class="col-sm-3">
        <select name="role" class="form-select">
          <option value="">All Roles</option>
          <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Customer</option>
          <option value="vendor"   {{ request('role') == 'vendor'   ? 'selected' : '' }}>Vendor</option>
          <option value="admin"    {{ request('role') == 'admin'    ? 'selected' : '' }}>Admin</option>
        </select>
      </div>
      <div class="col-sm-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary flex-grow-1">
          <i class="ti ti-filter me-1"></i>Filter
        </button>
        <a href="{{ url('/admin/users') }}" class="btn btn-outline-secondary">Reset</a>
      </div>
    </form>
  </div>
</div>

{{-- Users Table --}}
<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="bg-body-tertiary">
        <tr>
          <th>User</th>
          <th>Email</th>
          <th>Role</th>
          <th>Joined</th>
          <th>Orders</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
        <tr>
          <td>
            <div class="d-flex align-items-center gap-3">
              @php $ai = ($loop->index % 14) + 1; @endphp
              @if($user->profile_photo_path ?? false)
                <img src="{{ asset('storage/' . $user->profile_photo_path) }}"
                     alt="{{ $user->name }}"
                     style="width:38px;height:38px;border-radius:50%;object-fit:cover;" />
              @else
                <img src="{{ Vite::asset('resources/images/avatars/avatar-' . $ai . '.png') }}"
                     onerror="this.src='{{ Vite::asset('resources/images/misc/misc-1.png') }}'"
                     alt="{{ $user->name }}"
                     style="width:38px;height:38px;border-radius:50%;object-fit:cover;" />
              @endif
              <div>
                <div class="fw-semibold">{{ $user->name }}</div>
                <div class="text-muted small">ID: #{{ $user->id }}</div>
              </div>
            </div>
          </td>
          <td class="text-muted">{{ $user->email }}</td>
          <td>
            @foreach($user->roles ?? [] as $role)
              <span class="badge bg-label-primary">{{ $role->name }}</span>
            @endforeach
            @if(empty($user->roles ?? []))
              <span class="badge bg-label-secondary">Customer</span>
            @endif
          </td>
          <td class="text-muted small">{{ $user->created_at->format('M d, Y') }}</td>
          <td>
            <span class="fw-semibold">{{ $user->orders_count ?? 0 }}</span>
          </td>
          <td>
            @if($user->email_verified_at)
              <span class="badge bg-label-success">Verified</span>
            @else
              <span class="badge bg-label-warning">Unverified</span>
            @endif
          </td>
          <td>
            <div class="d-flex gap-1">
              <button class="btn btn-icon btn-sm btn-outline-primary" title="View user"
                      data-bs-toggle="tooltip">
                <i class="ti ti-eye"></i>
              </button>
              <button class="btn btn-icon btn-sm btn-outline-secondary" title="Edit user"
                      data-bs-toggle="modal" data-bs-target="#modal-edit-user">
                <i class="ti ti-edit"></i>
              </button>
              <button class="btn btn-icon btn-sm btn-outline-danger" title="Delete user"
                      onclick="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.')">
                <i class="ti ti-trash"></i>
              </button>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="text-center py-5">
            <img src="{{ Vite::asset('resources/images/pages/misc-under-maintenance.png') }}"
                 style="width:100px;opacity:.5;" alt="No users" class="mb-3 d-block mx-auto" />
            <div class="text-muted">No users found matching your criteria</div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($users->hasPages())
  <div class="card-footer border-0">
    <div class="d-flex justify-content-center">
      {{ $users->withQueryString()->links() }}
    </div>
  </div>
  @endif
</div>

{{-- Edit User Modal (reuse existing) --}}
@include('_partials._modals.modal-edit-user')

@endsection

@section('page-script')
<script>
  // Initialize tooltips
  var tooltipEls = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipEls.forEach(function(el) { new bootstrap.Tooltip(el); });
</script>
@endsection
