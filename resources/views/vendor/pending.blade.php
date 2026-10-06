@extends('layouts/layoutMaster')

@section('title', 'Application Pending')

@section('content')
<div class="row justify-content-center py-5">
  <div class="col-md-7 col-lg-5 text-center">
    <div class="card border-0 shadow-sm p-4">
      <div class="card-body">
        <div class="avatar avatar-xl bg-label-warning rounded-circle mx-auto mb-4">
          <i class="ti ti-clock ti-36px text-warning"></i>
        </div>
        <h4 class="fw-bold mb-2">Application Under Review</h4>
        <p class="text-muted mb-4">
          Thank you for registering <strong>{{ $vendor->store_name }}</strong>! Your store application has status 
          <span class="badge bg-label-warning">{{ ucfirst($vendor->approval_status) }}</span>.
          Our admin team is currently verifying your store details.
        </p>

        <div class="alert alert-info text-start small mb-4">
          <i class="ti ti-info-circle me-1"></i>
          Once approved, you will have full access to list products, manage inventory, and process incoming orders.
        </div>

        <a href="{{ route('home') }}" class="btn btn-primary w-100">
          <i class="ti ti-arrow-left me-1"></i> Return to Homepage
        </a>
      </div>
    </div>
  </div>
</div>
@endsection
