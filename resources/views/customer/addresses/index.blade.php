@extends('layouts/layoutMaster')

@section('title', 'Saved Addresses')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="fw-bold mb-1">Saved Addresses</h4>
    <p class="text-muted mb-0">Manage your shipping and delivery addresses</p>
  </div>
  <div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
      <i class="ti ti-plus me-1"></i>Add New Address
    </button>
  </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <ul class="mb-0">
    @foreach($errors->all() as $err)
      <li>{{ $err }}</li>
    @endforeach
  </ul>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row g-4">
  @forelse($addresses as $address)
  <div class="col-md-6 col-xl-4">
    <div class="card h-100 border-0 shadow-sm position-relative">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-label-{{ $address->type === 'Home' ? 'primary' : ($address->type === 'Office' ? 'info' : 'secondary') }}">
              <i class="ti ti-{{ $address->type === 'Home' ? 'home' : ($address->type === 'Office' ? 'building' : 'map-pin') }} me-1"></i>
              {{ $address->type }}
            </span>
            @if($address->is_default)
            <span class="badge bg-label-success">Default</span>
            @endif
          </div>
          <div class="dropdown">
            <button class="btn btn-text-secondary btn-icon rounded-pill p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="ti ti-dots-vertical"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editAddressModal-{{ $address->id }}">
                  <i class="ti ti-pencil me-2"></i>Edit
                </button>
              </li>
              <li>
                <form action="{{ route('customer.addresses.destroy', $address->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this address?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="dropdown-item text-danger">
                    <i class="ti ti-trash me-2"></i>Delete
                  </button>
                </form>
              </li>
            </ul>
          </div>
        </div>

        <h6 class="fw-bold mb-1">{{ $address->full_name }}</h6>
        @if($address->phone)
        <p class="text-muted small mb-2"><i class="ti ti-phone me-1"></i>{{ $address->phone }}</p>
        @endif

        <p class="mb-0 text-muted small">
          {{ $address->address_line1 }}<br>
          @if($address->address_line2){{ $address->address_line2 }}<br>@endif
          {{ $address->city }}{{ $address->state ? ', ' . $address->state : '' }} {{ $address->zip_code }}<br>
          {{ $address->country }}
        </p>
      </div>
      <div class="card-footer bg-transparent border-top d-flex gap-2 justify-content-end p-3">
        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editAddressModal-{{ $address->id }}">
          <i class="ti ti-pencil me-1"></i>Edit
        </button>
        <form action="{{ route('customer.addresses.destroy', $address->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this address?');">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-outline-danger btn-sm">
            <i class="ti ti-trash me-1"></i>Delete
          </button>
        </form>
      </div>
    </div>
  </div>

  {{-- Edit Modal for this Address --}}
  <div class="modal fade" id="editAddressModal-{{ $address->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <form action="{{ route('customer.addresses.update', $address->id) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="modal-header">
            <h5 class="modal-title">Edit Address</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body row g-3">
            <div class="col-md-4">
              <label class="form-label">Address Type</label>
              <select name="type" class="form-select" required>
                <option value="Home" {{ $address->type === 'Home' ? 'selected' : '' }}>Home</option>
                <option value="Office" {{ $address->type === 'Office' ? 'selected' : '' }}>Office</option>
                <option value="Other" {{ $address->type === 'Other' ? 'selected' : '' }}>Other</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">First Name</label>
              <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $address->first_name) }}" required />
            </div>
            <div class="col-md-4">
              <label class="form-label">Last Name</label>
              <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $address->last_name) }}" required />
            </div>
            <div class="col-12">
              <label class="form-label">Address Line 1</label>
              <input type="text" name="address_line1" class="form-control" value="{{ old('address_line1', $address->address_line1) }}" placeholder="Street address or P.O. Box" required />
            </div>
            <div class="col-12">
              <label class="form-label">Address Line 2 (Optional)</label>
              <input type="text" name="address_line2" class="form-control" value="{{ old('address_line2', $address->address_line2) }}" placeholder="Apt, Suite, Unit, Building" />
            </div>
            <div class="col-md-4">
              <label class="form-label">City</label>
              <input type="text" name="city" class="form-control" value="{{ old('city', $address->city) }}" required />
            </div>
            <div class="col-md-4">
              <label class="form-label">State / Province</label>
              <input type="text" name="state" class="form-control" value="{{ old('state', $address->state) }}" />
            </div>
            <div class="col-md-4">
              <label class="form-label">Postal / ZIP Code</label>
              <input type="text" name="zip_code" class="form-control" value="{{ old('zip_code', $address->zip_code) }}" required />
            </div>
            <div class="col-md-6">
              <label class="form-label">Country</label>
              <input type="text" name="country" class="form-control" value="{{ old('country', $address->country) }}" required />
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone (Optional)</label>
              <input type="text" name="phone" class="form-control" value="{{ old('phone', $address->phone) }}" />
            </div>
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_default" value="1" id="defaultCheck-{{ $address->id }}" {{ $address->is_default ? 'checked' : '' }} />
                <label class="form-check-label" for="defaultCheck-{{ $address->id }}">
                  Set as default shipping address
                </label>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  @empty
  <div class="col-12">
    <div class="card border-0 shadow-sm text-center py-5">
      <div class="card-body">
        <i class="ti ti-map-pin-off display-4 text-muted mb-3 d-block"></i>
        <h5>No Saved Addresses</h5>
        <p class="text-muted">You haven't added any shipping addresses yet.</p>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
          <i class="ti ti-plus me-1"></i>Add Address
        </button>
      </div>
    </div>
  </div>
  @endforelse
</div>

{{-- Add Address Modal --}}
<div class="modal fade" id="addAddressModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('customer.addresses.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Add New Address</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body row g-3">
          <div class="col-md-4">
            <label class="form-label">Address Type</label>
            <select name="type" class="form-select" required>
              <option value="Home" selected>Home</option>
              <option value="Office">Office</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">First Name</label>
            <input type="text" name="first_name" class="form-control" value="{{ old('first_name', auth()->user() ? explode(' ', auth()->user()->name)[0] : '') }}" required />
          </div>
          <div class="col-md-4">
            <label class="form-label">Last Name</label>
            <input type="text" name="last_name" class="form-control" value="{{ old('last_name', auth()->user() && str_contains(auth()->user()->name, ' ') ? substr(auth()->user()->name, strpos(auth()->user()->name, ' ') + 1) : 'Customer') }}" required />
          </div>
          <div class="col-12">
            <label class="form-label">Address Line 1</label>
            <input type="text" name="address_line1" class="form-control" value="{{ old('address_line1') }}" placeholder="Street address or P.O. Box" required />
          </div>
          <div class="col-12">
            <label class="form-label">Address Line 2 (Optional)</label>
            <input type="text" name="address_line2" class="form-control" value="{{ old('address_line2') }}" placeholder="Apt, Suite, Unit, Building" />
          </div>
          <div class="col-md-4">
            <label class="form-label">City</label>
            <input type="text" name="city" class="form-control" value="{{ old('city') }}" required />
          </div>
          <div class="col-md-4">
            <label class="form-label">State / Province</label>
            <input type="text" name="state" class="form-control" value="{{ old('state') }}" />
          </div>
          <div class="col-md-4">
            <label class="form-label">Postal / ZIP Code</label>
            <input type="text" name="zip_code" class="form-control" value="{{ old('zip_code') }}" required />
          </div>
          <div class="col-md-6">
            <label class="form-label">Country</label>
            <input type="text" name="country" class="form-control" value="{{ old('country', 'United States') }}" required />
          </div>
          <div class="col-md-6">
            <label class="form-label">Phone (Optional)</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" />
          </div>
          <div class="col-12">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_default" value="1" id="addDefaultCheck" checked />
              <label class="form-check-label" for="addDefaultCheck">
                Set as default shipping address
              </label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Address</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
