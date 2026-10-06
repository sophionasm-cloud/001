@extends('layouts/blankLayout')

@section('title', 'Vendor Login')

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
<div class="authentication-wrapper authentication-cover authentication-bg">
  <div class="authentication-inner row">

    {{-- Left illustration --}}
    <div class="d-none d-lg-flex col-lg-7 p-0">
      <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
        <img src="{{ Vite::asset('resources/images/pages/auth-v2-login-illustration-bordered-light.png') }}"
             onerror="this.src='{{ Vite::asset('resources/images/pages/auth-v2-login-illustration-light.png') }}'"
             alt="Vendor Login"
             class="img-fluid my-auto"
             style="max-width: 80%" />
      </div>
    </div>

    {{-- Right form --}}
    <div class="d-flex col-12 col-lg-5 align-items-center p-sm-5 p-4">
      <div class="w-px-400 mx-auto">
        <div class="app-brand mb-4">
          <a href="{{ url('/') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
              <img src="{{ Vite::asset('resources/images/logo.svg') }}"
                   onerror="this.style.display='none'" alt="Logo" style="height:28px;" />
            </span>
            <span class="app-brand-text demo text-body fw-bolder ms-2">MultiVendor</span>
          </a>
        </div>

        <h4 class="mb-1">Vendor Portal 🏪</h4>
        <p class="mb-4 text-muted">Sign in to manage your products and orders</p>

        <div class="d-flex align-items-center gap-2 p-3 bg-label-primary rounded mb-4">
          <i class="ti ti-building-store text-primary ti-lg"></i>
          <div>
            <div class="fw-semibold small">Merchant Hub</div>
            <div class="text-muted" style="font-size:.75rem">Manage inventory, fulfillment & metrics</div>
          </div>
        </div>

        @if($errors->any())
        <div class="alert alert-danger mb-3">
          <ul class="mb-0">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
          </ul>
        </div>
        @endif

        <form action="{{ url('/login') }}" method="POST">
          @csrf
          <div class="mb-3">
            <label for="email" class="form-label">Vendor Email</label>
            <input type="email" class="form-control" id="email" name="email"
                   placeholder="vendor@example.com" value="{{ old('email') }}" autofocus required />
          </div>
          <div class="mb-3 form-password-toggle">
            <div class="d-flex justify-content-between">
              <label class="form-label" for="password">Password</label>
              <a href="{{ url('/forgot-password') }}" class="small">Forgot?</a>
            </div>
            <div class="input-group input-group-merge">
              <input type="password" id="password" class="form-control form-control-merge"
                     name="password" placeholder="············" required />
              <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
            </div>
          </div>
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="remember" name="remember" />
              <label class="form-check-label" for="remember">Remember me</label>
            </div>
          </div>
          <button class="btn btn-primary d-grid w-100 mb-3" type="submit">
            <i class="ti ti-login me-1"></i> Sign In to Store
          </button>
        </form>

        <p class="text-center">
          <span>New seller?</span>
          <a href="{{ route('vendor.register') }}"><strong>Register your store</strong></a>
        </p>

        <p class="text-center mt-3">
          <a href="{{ url('/customer/login') }}" class="text-muted small me-3">Customer Login</a>
          <a href="{{ url('/admin/login') }}" class="text-muted small">Admin Login</a>
        </p>
      </div>
    </div>

  </div>
</div>
@endsection
