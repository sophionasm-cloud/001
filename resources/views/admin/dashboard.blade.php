@extends('layouts/layoutMaster')

@section('title', 'Admin Dashboard')

@section('content')

<div class="row g-4">

  {{-- Welcome Header --}}
  <div class="col-12">
    <div class="card border-0 shadow-sm overflow-hidden"
         style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
      <div class="card-body p-5 position-relative">
        <div class="row align-items-center">
          <div class="col-md-7 text-white">
            <h3 class="fw-bold mb-1">Welcome back, {{ auth()->user()->name ?? 'Admin' }}! 👋</h3>
            <p class="opacity-75 mb-3">Here is what is happening across your multi-vendor platform today.</p>
            <div class="d-flex gap-2">
              <a href="{{ route('admin.vendors.pending') }}" class="btn btn-light btn-sm">
                <i class="ti ti-users me-1"></i>Review Vendors
              </a>
              <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-light btn-sm">
                <i class="ti ti-chart-bar me-1"></i>View Reports
              </a>
            </div>
          </div>
          <div class="col-md-5 text-end d-none d-md-block">
            <img src="{{ Vite::asset('resources/images/pages/boy-illustration.png') }}"
                 onerror="this.src='{{ Vite::asset('resources/images/misc/misc-1.png') }}'"
                 alt="Dashboard" style="max-height:160px;opacity:.9;" />
          </div>
        </div>
        <div style="position:absolute;top:-30px;right:-30px;width:150px;height:150px;border-radius:50%;background:rgba(255,255,255,.1);"></div>
        <div style="position:absolute;bottom:-50px;right:100px;width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,.07);"></div>
      </div>
    </div>
  </div>

  {{-- Stats Cards --}}
  <div class="col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-lg bg-label-primary rounded">
          <i class="ti ti-users ti-28px text-primary"></i>
        </div>
        <div>
          <div class="text-muted small">Total Customers</div>
          <h4 class="mb-0 fw-bold">{{ $totalCustomers ?? 0 }}</h4>
          <div class="text-success small"><i class="ti ti-trending-up"></i> Registered shoppers</div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-lg bg-label-success rounded">
          <i class="ti ti-shopping-bag ti-28px text-success"></i>
        </div>
        <div>
          <div class="text-muted small">Total Orders</div>
          <h4 class="mb-0 fw-bold">{{ $totalOrders ?? 0 }}</h4>
          <div class="text-success small"><i class="ti ti-trending-up"></i> All-time orders</div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-lg bg-label-warning rounded">
          <i class="ti ti-store ti-28px text-warning"></i>
        </div>
        <div>
          <div class="text-muted small">Active Vendors</div>
          <h4 class="mb-0 fw-bold">{{ $totalVendors ?? 0 }}</h4>
          <div class="text-warning small"><i class="ti ti-clock"></i> {{ $pendingVendors ?? 0 }} pending</div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-lg bg-label-info rounded">
          <i class="ti ti-currency-dollar ti-28px text-info"></i>
        </div>
        <div>
          <div class="text-muted small">Total Revenue</div>
          <h4 class="mb-0 fw-bold">${{ number_format($totalRevenue ?? 0, 2) }}</h4>
          <div class="text-success small"><i class="ti ti-trending-up"></i> Total gross sales</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Quick Links & Pending Review --}}
  <div class="col-xl-8">
    <div class="card border-0 shadow-sm">
      <div class="card-header d-flex align-items-center justify-content-between border-0">
        <h6 class="fw-bold mb-0">Platform Quick Actions</h6>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <a href="{{ route('admin.vendors.pending') }}" class="card text-decoration-none border shadow-none p-3 h-100 hover-elevate">
              <div class="d-flex align-items-center gap-3">
                <div class="avatar bg-label-warning rounded">
                  <i class="ti ti-user-check text-warning"></i>
                </div>
                <div>
                  <h6 class="mb-0 fw-semibold text-body">Pending Vendors</h6>
                  <small class="text-muted">{{ $pendingVendors ?? 0 }} waiting</small>
                </div>
              </div>
            </a>
          </div>
          <div class="col-md-4">
            <a href="{{ route('admin.vendors.list') }}" class="card text-decoration-none border shadow-none p-3 h-100 hover-elevate">
              <div class="d-flex align-items-center gap-3">
                <div class="avatar bg-label-primary rounded">
                  <i class="ti ti-building-store text-primary"></i>
                </div>
                <div>
                  <h6 class="mb-0 fw-semibold text-body">Vendors List</h6>
                  <small class="text-muted">Manage stores</small>
                </div>
              </div>
            </a>
          </div>
          <div class="col-md-4">
            <a href="{{ route('admin.users.index') }}" class="card text-decoration-none border shadow-none p-3 h-100 hover-elevate">
              <div class="d-flex align-items-center gap-3">
                <div class="avatar bg-label-success rounded">
                  <i class="ti ti-users text-success"></i>
                </div>
                <div>
                  <h6 class="mb-0 fw-semibold text-body">Users & Roles</h6>
                  <small class="text-muted">Manage accounts</small>
                </div>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Pending Vendors Notice --}}
  <div class="col-xl-4">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header d-flex align-items-center justify-content-between border-0">
        <h6 class="fw-bold mb-0">Vendor Approvals</h6>
        @if(($pendingVendors ?? 0) > 0)
          <span class="badge bg-warning text-dark">{{ $pendingVendors }} Pending</span>
        @endif
      </div>
      <div class="card-body text-center py-4">
        @if(($pendingVendors ?? 0) > 0)
          <img src="{{ Vite::asset('resources/images/svg/paper-send.svg') }}" style="width:50px;" class="mb-3" />
          <h6>{{ $pendingVendors }} New Application(s)</h6>
          <p class="text-muted small">Vendors are waiting for super admin approval before they can sell.</p>
          <a href="{{ route('admin.vendors.pending') }}" class="btn btn-warning btn-sm">
            Review Applications
          </a>
        @else
          <img src="{{ Vite::asset('resources/images/svg/Check.svg') }}" style="width:50px;" class="mb-3" />
          <h6>All Up to Date!</h6>
          <p class="text-muted small">No pending vendor applications waiting for review.</p>
          <a href="{{ route('admin.vendors.list') }}" class="btn btn-outline-primary btn-sm">
            View All Vendors
          </a>
        @endif
      </div>
    </div>
  </div>

  {{-- Platform Highlights --}}
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-header border-0">
        <h6 class="fw-bold mb-0">Platform Highlights</h6>
      </div>
      <div class="card-body">
        <div class="row g-3">
          @foreach([1,2,3,4] as $n)
          <div class="col-sm-6 col-lg-3">
            <div class="rounded overflow-hidden" style="height:120px;">
              <img src="{{ Vite::asset('resources/images/pages/TimelineRectangle' . $n . '.png') }}"
                   alt="Highlight {{ $n }}"
                   style="width:100%;height:100%;object-fit:cover;" />
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
