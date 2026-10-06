@extends('layouts/blankLayout')

@section('title', 'Create Account')

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
<div class="authentication-wrapper authentication-cover authentication-bg">
  <div class="authentication-inner row">

    {{-- Left panel --}}
    <div class="d-none d-lg-flex col-lg-7 p-0">
      <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
        <img src="{{ Vite::asset('resources/images/pages/auth-v2-register-illustration-light.png') }}"
             alt="Register"
             class="img-fluid my-auto"
             style="max-width: 75%"
             data-app-light-img="pages/auth-v2-register-illustration-light.png"
             data-app-dark-img="pages/auth-v2-register-illustration-dark.png" />
      </div>
    </div>

    {{-- Right: Registration Form --}}
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
        <h4 class="mb-1">Create Your Account 🚀</h4>
        <p class="mb-4 text-muted">Join thousands of shoppers and vendors!</p>

        @if($errors->any())
        <div class="alert alert-danger mb-3">
          <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form action="{{ url('/register') }}" method="POST">
          @csrf

          {{-- Account type --}}
          <div class="mb-3">
            <label class="form-label">I want to…</label>
            <div class="row g-2">
              <div class="col-6">
                <label class="card border-2 p-3 text-center cursor-pointer"
                       style="cursor:pointer;transition:border-color .2s;"
                       id="type-customer-label">
                  <input type="radio" name="account_type" value="customer" class="d-none" checked
                         onchange="setType('customer')" />
                  <img src="{{ Vite::asset('resources/images/svg/user.svg') }}"
                       style="width:32px;" alt="Customer" class="mb-2" />
                  <div class="fw-semibold small">Shop & Buy</div>
                  <div class="text-muted" style="font-size:.7rem;">Customer Account</div>
                </label>
              </div>
              <div class="col-6">
                <label class="card border-2 p-3 text-center cursor-pointer"
                       id="type-vendor-label">
                  <input type="radio" name="account_type" value="vendor" class="d-none"
                         onchange="setType('vendor')" />
                  <img src="{{ Vite::asset('resources/images/svg/Suitcase.svg') }}"
                       style="width:32px;" alt="Vendor" class="mb-2" />
                  <div class="fw-semibold small">Sell Products</div>
                  <div class="text-muted" style="font-size:.7rem;">Vendor Account</div>
                </label>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror"
                   id="name" name="name" value="{{ old('name') }}"
                   placeholder="John Doe" required />
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          {{-- Vendor-only field --}}
          <div class="mb-3 d-none" id="store-name-field">
            <label for="store_name" class="form-label">Store Name</label>
            <input type="text" class="form-control" id="store_name" name="store_name"
                   value="{{ old('store_name') }}" placeholder="Your Store Name" />
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" class="form-control @error('email') is-invalid @enderror"
                   id="email" name="email" value="{{ old('email') }}"
                   placeholder="john@example.com" required />
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3 form-password-toggle">
            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
            <div class="input-group input-group-merge">
              <input type="password" class="form-control form-control-merge @error('password') is-invalid @enderror"
                     id="password" name="password" placeholder="············" required />
              <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
              @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="mb-3 form-password-toggle">
            <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
            <div class="input-group input-group-merge">
              <input type="password" class="form-control form-control-merge"
                     id="password_confirmation" name="password_confirmation"
                     placeholder="············" required />
              <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
            </div>
          </div>

          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="terms" name="terms" required />
              <label class="form-check-label" for="terms">
                I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
              </label>
            </div>
          </div>

          <button class="btn btn-primary d-grid w-100 mb-3" type="submit">Create Account</button>

          <p class="text-center">
            Already have an account? <a href="{{ url('/login') }}"><strong>Sign in</strong></a>
          </p>
        </form>
      </div>
    </div>

  </div>
</div>
@endsection

@section('page-script')
<script>
  function setType(type) {
    const customerLabel = document.getElementById('type-customer-label');
    const vendorLabel = document.getElementById('type-vendor-label');
    const storeField = document.getElementById('store-name-field');

    if (type === 'vendor') {
      vendorLabel.classList.add('border-primary');
      customerLabel.classList.remove('border-primary');
      storeField.classList.remove('d-none');
    } else {
      customerLabel.classList.add('border-primary');
      vendorLabel.classList.remove('border-primary');
      storeField.classList.add('d-none');
    }
  }
  // Initialize
  setType('customer');
</script>
@endsection
