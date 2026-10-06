@extends('layouts/blankLayout')

@section('title', 'Register Vendor Store')

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
<div class="authentication-wrapper authentication-cover authentication-bg">
  <div class="authentication-inner row">

    {{-- Left Panel --}}
    <div class="d-none d-lg-flex col-lg-7 p-0">
      <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
        <img src="{{ Vite::asset('resources/images/pages/auth-v2-register-illustration-light.png') }}"
             alt="Vendor Register"
             class="img-fluid my-auto"
             style="max-width: 75%" />
      </div>
    </div>

    {{-- Right: Form --}}
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

        <h4 class="mb-1">Open Your Vendor Store 🏪</h4>
        <p class="mb-4 text-muted">Complete your seller profile to start selling</p>

        @if($errors->any())
        <div class="alert alert-danger mb-3">
          <ul class="mb-0">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
          </ul>
        </div>
        @endif

        <form action="{{ route('vendor.register.submit') }}" method="POST">
          @csrf
          <input type="hidden" name="user_id" value="{{ auth()->id() ?? 2 }}" />

          <div class="mb-3">
            <label class="form-label" for="store_name">Store Name <span class="text-danger">*</span></label>
            <input type="text" name="store_name" id="store_name" class="form-control"
                   placeholder="e.g. Acme Tech World" value="{{ old('store_name') }}" required />
          </div>

          <div class="mb-4">
            <label class="form-label" for="description">Store Description</label>
            <textarea name="description" id="description" class="form-control" rows="4"
                      placeholder="Tell customers about your products and services...">{{ old('description') }}</textarea>
          </div>

          <button class="btn btn-primary d-grid w-100 mb-3" type="submit">
            <i class="ti ti-check me-1"></i> Register Store
          </button>

          <p class="text-center">
            <a href="{{ route('home') }}" class="text-muted small">
              <i class="ti ti-arrow-left me-1"></i> Return to Homepage
            </a>
          </p>
        </form>
      </div>
    </div>

  </div>
</div>
@endsection
