@extends('layouts/blankLayout')

@section('title', 'Login')

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
<div class="authentication-wrapper authentication-cover authentication-bg">
  <div class="authentication-inner row">

    <!-- Left brand panel -->
    <div class="d-none d-lg-flex col-lg-7 p-0">
      <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
        <img src="{{ asset('assets/img/illustrations/auth-cover-login-illustration-light.png') }}"
          alt="auth-login-cover" class="img-fluid my-auto" style="max-width: 80%"
          data-app-light-img="illustrations/auth-cover-login-illustration-light.png"
          data-app-dark-img="illustrations/auth-cover-login-illustration-dark.png" />
      </div>
    </div>

    <!-- Right login form -->
    <div class="d-flex col-12 col-lg-5 align-items-center p-sm-5 p-4">
      <div class="w-px-400 mx-auto">
        <div class="app-brand mb-4">
          <a href="{{ url('/') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
              <span style="color: var(--bs-primary);">
                <svg width="25" viewBox="0 0 25 42" version="1.1" xmlns="http://www.w3.org/2000/svg">
                  <defs><path d="M13.7918663,0.358365126 L3.39788168,7.44174259 C0.566865006,9.69408244 -0.379795186,13.4788597 0.905203999,16.7215756 L4.58895399,26.0695904 L13.7918663,0.358365126 Z" id="path-1"></path><path d="M17.4239992,35.4837396 C15.6732428,35.9154754 13.8256914,36.1292614 11.9770485,36.1292614 C7.72985063,36.1292614 4.0518807,34.4730959 1.41180672,31.7694408 L6.23914066,44.5623775 L22.7260978,34.7931194 L17.4239992,35.4837396 Z" id="path-3"></path><path d="M6.23914066,44.5623775 L2.56164584,35.1068079 C2.10063177,34.0749738 1.95354386,32.9583488 2.10063177,31.8739138 L4.58895399,26.0695904 L1.41180672,31.7694408 L6.23914066,44.5623775 Z" id="path-4"></path></defs>
                  <g id="v2" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <polygon id="Path-1" fill="currentColor" opacity="0.2" points="0 0 13.7918663 0.358365126 8.58800532 10.7625701"></polygon>
                    <polygon id="Path-2" fill="currentColor" points="13.7918663 0.358365126 22.5751552 9.28137278 8.58800532 10.7625701"></polygon>
                  </g>
                </svg>
              </span>
            </span>
            <span class="app-brand-text demo text-body fw-bolder ms-2">MultiVendor</span>
          </a>
        </div>
        <h4 class="mb-1">Welcome to MultiVendor Store! 👋</h4>
        <p class="mb-4">Please sign-in to your account and start the adventure</p>

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

        <form id="formAuthentication" class="mb-3" action="{{ url('/login') }}" method="POST">
          @csrf
          <div class="mb-3">
            <label for="email" class="form-label">Email or Username</label>
            <input type="text" class="form-control" id="email" name="email"
              placeholder="Enter your email" value="{{ old('email') }}" autofocus />
          </div>
          <div class="mb-3 form-password-toggle">
            <div class="d-flex justify-content-between">
              <label class="form-label" for="password">Password</label>
            </div>
            <div class="input-group input-group-merge">
              <input type="password" id="password" class="form-control form-control-merge"
                name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                aria-describedby="password" />
              <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
            </div>
          </div>
          <div class="mb-3">
            <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
          </div>
        </form>
        <p class="text-center">
          <span>New on our platform?</span>
          <a href="{{ url('/register') }}"><span>Create an account</span></a>
        </p>
        <p class="text-center mt-2">
          <a href="{{ url('/vendor/login') }}">Login as Vendor</a> &nbsp;|&nbsp;
          <a href="{{ url('/admin/login') }}">Login as Admin</a>
        </p>
      </div>
    </div>
    <!-- /Right login form -->
  </div>
</div>
@endsection
