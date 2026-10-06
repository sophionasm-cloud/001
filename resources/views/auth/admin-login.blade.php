@extends('layouts/blankLayout')

@section('title', 'Admin Login')

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
<div class="authentication-wrapper authentication-cover authentication-bg">
  <div class="authentication-inner row">

    {{-- Left panel with illustration --}}
    <div class="d-none d-lg-flex col-lg-7 p-0">
      <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
        <img src="{{ Vite::asset('resources/images/pages/auth-v2-login-illustration-light.png') }}"
             alt="Admin Login Cover"
             class="img-fluid my-auto"
             style="max-width: 75%"
             data-app-light-img="pages/auth-v2-login-illustration-light.png"
             data-app-dark-img="pages/auth-v2-login-illustration-dark.png" />
      </div>
    </div>

    {{-- Right login form --}}
    <div class="d-flex col-12 col-lg-5 align-items-center p-sm-5 p-4">
      <div class="w-px-400 mx-auto">

        {{-- Brand --}}
        <div class="app-brand mb-4">
          <a href="{{ url('/') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
              <img src="{{ Vite::asset('resources/images/logo.svg') }}"
                   onerror="this.style.display='none'"
                   alt="Logo" style="height:32px;" />
            </span>
            <span class="app-brand-text demo text-body fw-bolder ms-2">MultiVendor</span>
          </a>
        </div>

        {{-- Heading --}}
        <div class="mb-4">
          <h4 class="mb-1">Admin Portal 🔐</h4>
          <p class="text-muted">Sign in with your administrator credentials</p>
        </div>

        {{-- Admin badge --}}
        <div class="d-flex align-items-center gap-2 p-3 bg-label-danger rounded mb-4">
          <i class="ti ti-shield-lock text-danger ti-lg"></i>
          <div>
            <div class="fw-semibold small">Restricted Access</div>
            <div class="text-muted" style="font-size:.75rem">Super Admin credentials required</div>
          </div>
        </div>

        @if($errors->any())
        <div class="alert alert-danger mb-3">
          <ul class="mb-0">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
        @endif

        <form action="{{ url('/login') }}" method="POST">
          @csrf
          <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="email"
                   placeholder="admin@example.com" value="{{ old('email') }}" autofocus required />
          </div>
          <div class="mb-3 form-password-toggle">
            <label class="form-label" for="password">Password</label>
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
          <button class="btn btn-danger d-grid w-100" type="submit">
            <i class="ti ti-login me-2"></i>Sign In as Admin
          </button>
        </form>

        <p class="text-center mt-4">
          <a href="{{ url('/login') }}" class="text-muted small">
            <i class="ti ti-arrow-left me-1"></i>Back to Customer Login
          </a>
        </p>
      </div>
    </div>

  </div>
</div>
@endsection
