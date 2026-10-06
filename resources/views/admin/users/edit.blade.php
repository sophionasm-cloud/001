@extends('layouts/layoutMaster')

@section('title', 'Edit User')

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
  <a href="{{ route('admin.users.index') }}" class="btn btn-icon btn-outline-secondary">
    <i class="ti ti-arrow-left"></i>
  </a>
  <div>
    <h4 class="fw-bold mb-0">Edit User: {{ $user->name }}</h4>
    <p class="text-muted mb-0 small">Update user credentials and role assignment</p>
  </div>
</div>

@if($errors->any())
  <div class="alert alert-danger mb-4">
    <ul class="mb-0">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="card border-0 shadow-sm" style="max-width: 600px;">
  <div class="card-body">
    <form action="{{ url('admin/users/' . $user->id) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required />
      </div>

      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required />
      </div>

      <div class="mb-4">
        <label class="form-label">Role</label>
        <select name="role_id" class="form-select" required>
          <option value="1" {{ old('role_id', $user->role_id) == 1 ? 'selected' : '' }}>Super Admin</option>
          <option value="2" {{ old('role_id', $user->role_id) == 2 ? 'selected' : '' }}>Vendor</option>
          <option value="3" {{ old('role_id', $user->role_id) == 3 ? 'selected' : '' }}>Customer</option>
        </select>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4">
          <i class="ti ti-check me-1"></i> Update User
        </button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>

@endsection
