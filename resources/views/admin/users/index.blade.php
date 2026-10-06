@extends('layouts/layoutMaster')

@section('title', 'Users List')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="fw-bold mb-1">Users Management</h4>
    <p class="text-muted mb-0">Total of {{ $users->total() }} users on the platform</p>
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
    <table class="table table-hover align-middle mb-0">
      <thead class="bg-body-tertiary">
        <tr>
          <th>User</th>
          <th>Email</th>
          <th>Role</th>
          <th>Joined</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
        <tr>
          <td>
            <div class="d-flex align-items-center gap-3">
              @php $ai = ($loop->index % 14) + 1; @endphp
              <img src="{{ Vite::asset('resources/images/avatars/avatar-' . $ai . '.png') }}"
                   onerror="this.src='{{ Vite::asset('resources/images/misc/misc-1.png') }}'"
                   alt="{{ $user->name }}"
                   style="width:36px;height:36px;border-radius:50%;object-fit:cover;" />
              <div>
                <span class="fw-semibold">{{ $user->name }}</span>
                <div class="text-muted small">ID: #{{ $user->id }}</div>
              </div>
            </div>
          </td>
          <td>{{ $user->email }}</td>
          <td>
            <span class="badge bg-label-primary">
              {{ $user->role->name ?? 'User' }}
            </span>
          </td>
          <td class="text-muted small">{{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</td>
          <td>
            <div class="d-flex gap-2">
              <a href="{{ url('admin/users/' . $user->id . '/edit') }}" class="btn btn-sm btn-outline-primary">
                <i class="ti ti-edit"></i> Edit
              </a>
              <form action="{{ url('admin/users/' . $user->id) }}" method="POST" onsubmit="return confirm('Delete this user?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">
                  <i class="ti ti-trash"></i> Delete
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center py-4 text-muted">No users found</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($users->hasPages())
    <div class="card-footer border-0">
      <div class="d-flex justify-content-center">
        {{ $users->links() }}
      </div>
    </div>
  @endif
</div>

@endsection
