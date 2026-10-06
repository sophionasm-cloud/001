@extends('layouts/layoutMaster')

@section('title', 'Checkout')

@section('page-style')
<style>
  .checkout-steps { counter-reset: step; }
  .checkout-step { display: flex; align-items: flex-start; gap: 1rem; padding-bottom: 1.5rem; }
  .checkout-step:not(:last-child) { border-left: 2px solid var(--bs-border-color); margin-left: 20px; }
  .step-icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; }
  .order-item-img { width: 56px; height: 56px; object-fit: cover; border-radius: .375rem; }
  .payment-method-card { border: 2px solid var(--bs-border-color); border-radius: .5rem; cursor: pointer; transition: border-color .2s, background .2s; }
  .payment-method-card:has(input:checked) { border-color: var(--bs-primary); background: var(--bs-primary-bg-subtle); }
</style>
@endsection

@section('content')

{{-- Header --}}
<div class="mb-5">
  <h4 class="fw-bold mb-1">
    <img src="{{ Vite::asset('resources/images/svg/payment.svg') }}" style="width:28px;" alt="" class="me-2" />
    Checkout
  </h4>
  <p class="text-muted mb-0">Complete your purchase securely</p>
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

<div class="row g-5">

  {{-- LEFT: Checkout Form --}}
  <div class="col-lg-7">
    <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
      @csrf

      {{-- Step 1: Contact Info --}}
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header border-0">
          <div class="d-flex align-items-center gap-3">
            <div class="step-icon bg-primary text-white">
              <img src="{{ Vite::asset('resources/images/svg/user-info.svg') }}" style="width:20px;filter:brightness(100);" alt="" />
            </div>
            <div>
              <h6 class="mb-0 fw-bold">Contact Information</h6>
              <small class="text-muted">Your personal details</small>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-sm-6">
              <label class="form-label">First Name <span class="text-danger">*</span></label>
              <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                     value="{{ old('first_name', auth()->user()->name ?? '') }}" required />
              @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-sm-6">
              <label class="form-label">Last Name <span class="text-danger">*</span></label>
              <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                     value="{{ old('last_name') }}" required />
              @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
              <label class="form-label">Email Address <span class="text-danger">*</span></label>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                     value="{{ old('email', auth()->user()->email ?? '') }}" required />
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
              <label class="form-label">Phone Number</label>
              <input type="tel" name="phone" class="form-control"
                     value="{{ old('phone') }}" placeholder="+1 (555) 000-0000" />
            </div>
          </div>
        </div>
      </div>

      {{-- Step 2: Shipping Address --}}
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header border-0">
          <div class="d-flex align-items-center gap-3">
            <div class="step-icon bg-info text-white">
              <img src="{{ Vite::asset('resources/images/svg/address.svg') }}" style="width:20px;filter:brightness(100);" alt="" />
            </div>
            <div>
              <h6 class="mb-0 fw-bold">Shipping Address</h6>
              <small class="text-muted">Where should we deliver?</small>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">Address Line 1 <span class="text-danger">*</span></label>
              <input type="text" name="address_line1" class="form-control @error('address_line1') is-invalid @enderror"
                     value="{{ old('address_line1') }}" placeholder="Street address, P.O. box" required />
              @error('address_line1')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
              <label class="form-label">Address Line 2</label>
              <input type="text" name="address_line2" class="form-control"
                     value="{{ old('address_line2') }}" placeholder="Apartment, suite, unit (optional)" />
            </div>
            <div class="col-sm-6">
              <label class="form-label">City <span class="text-danger">*</span></label>
              <input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
                     value="{{ old('city') }}" required />
              @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-sm-3">
              <label class="form-label">State</label>
              <input type="text" name="state" class="form-control" value="{{ old('state') }}" />
            </div>
            <div class="col-sm-3">
              <label class="form-label">ZIP Code <span class="text-danger">*</span></label>
              <input type="text" name="zip_code" class="form-control @error('zip_code') is-invalid @enderror"
                     value="{{ old('zip_code') }}" required />
              @error('zip_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
              <label class="form-label">Country <span class="text-danger">*</span></label>
              <select name="country" class="form-select" required>
                <option value="">Select Country</option>
                <option value="US" {{ old('country') == 'US' ? 'selected' : '' }}>United States</option>
                <option value="CA" {{ old('country') == 'CA' ? 'selected' : '' }}>Canada</option>
                <option value="GB" {{ old('country') == 'GB' ? 'selected' : '' }}>United Kingdom</option>
                <option value="ET" {{ old('country') == 'ET' ? 'selected' : '' }}>Ethiopia</option>
                <option value="AE" {{ old('country') == 'AE' ? 'selected' : '' }}>UAE</option>
                <option value="AU" {{ old('country') == 'AU' ? 'selected' : '' }}>Australia</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      {{-- Step 3: Payment --}}
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header border-0">
          <div class="d-flex align-items-center gap-3">
            <div class="step-icon bg-success text-white">
              <img src="{{ Vite::asset('resources/images/svg/Card.svg') }}" style="width:20px;filter:brightness(100);" alt="" />
            </div>
            <div>
              <h6 class="mb-0 fw-bold">Payment Method</h6>
              <small class="text-muted">Choose how you'd like to pay</small>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row g-3">

            <div class="col-sm-6">
              <label class="payment-method-card p-3 d-flex align-items-center gap-3 w-100">
                <input type="radio" name="payment_method" value="credit_card"
                       class="d-none" {{ old('payment_method', 'credit_card') == 'credit_card' ? 'checked' : '' }} />
                <img src="{{ Vite::asset('resources/images/svg/Card.svg') }}" style="width:32px;" alt="Card" />
                <div>
                  <div class="fw-semibold small">Credit / Debit Card</div>
                  <div class="text-muted" style="font-size:.75rem">Visa, Mastercard, Amex</div>
                </div>
              </label>
            </div>

            <div class="col-sm-6">
              <label class="payment-method-card p-3 d-flex align-items-center gap-3 w-100">
                <input type="radio" name="payment_method" value="paypal"
                       class="d-none" {{ old('payment_method') == 'paypal' ? 'checked' : '' }} />
                <img src="{{ Vite::asset('resources/images/svg/Wallet.svg') }}" style="width:32px;" alt="Wallet" />
                <div>
                  <div class="fw-semibold small">Digital Wallet</div>
                  <div class="text-muted" style="font-size:.75rem">PayPal, Apple Pay, etc.</div>
                </div>
              </label>
            </div>

            <div class="col-sm-6">
              <label class="payment-method-card p-3 d-flex align-items-center gap-3 w-100">
                <input type="radio" name="payment_method" value="cod"
                       class="d-none" {{ old('payment_method') == 'cod' ? 'checked' : '' }} />
                <img src="{{ Vite::asset('resources/images/svg/home.svg') }}" style="width:32px;" alt="COD" />
                <div>
                  <div class="fw-semibold small">Cash on Delivery</div>
                  <div class="text-muted" style="font-size:.75rem">Pay when you receive</div>
                </div>
              </label>
            </div>

            <div class="col-sm-6">
              <label class="payment-method-card p-3 d-flex align-items-center gap-3 w-100">
                <input type="radio" name="payment_method" value="bank"
                       class="d-none" {{ old('payment_method') == 'bank' ? 'checked' : '' }} />
                <img src="{{ Vite::asset('resources/images/svg/Diamond.svg') }}" style="width:32px;" alt="Bank" />
                <div>
                  <div class="fw-semibold small">Bank Transfer</div>
                  <div class="text-muted" style="font-size:.75rem">Direct bank transfer</div>
                </div>
              </label>
            </div>
          </div>

          {{-- Credit card fields (shown conditionally) --}}
          <div id="card-fields" class="mt-4">
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label">Card Number</label>
                <input type="text" name="card_number" class="form-control"
                       placeholder="1234 5678 9012 3456" maxlength="19" />
              </div>
              <div class="col-sm-6">
                <label class="form-label">Expiry Date</label>
                <input type="text" name="card_expiry" class="form-control" placeholder="MM / YY" />
              </div>
              <div class="col-sm-6">
                <label class="form-label">CVV</label>
                <input type="text" name="card_cvv" class="form-control" placeholder="123" maxlength="4" />
              </div>
            </div>
          </div>

          {{-- Additional notes --}}
          <div class="mt-4">
            <label class="form-label">Order Notes (optional)</label>
            <textarea name="notes" class="form-control" rows="2"
                      placeholder="Special delivery instructions…">{{ old('notes') }}</textarea>
          </div>
        </div>
      </div>

      <button type="submit" class="btn btn-success btn-lg w-100">
        <i class="ti ti-lock me-2"></i>Place Order Securely
      </button>
      <p class="text-center text-muted small mt-2">
        <img src="{{ Vite::asset('resources/images/svg/Check.svg') }}" style="width:14px;" alt="" class="me-1" />
        Your personal data will be used to process your order
      </p>
    </form>
  </div>

  {{-- RIGHT: Order Summary --}}
  <div class="col-lg-5">
    <div class="card border-0 shadow-sm sticky-top" style="top:80px">
      <div class="card-header border-0">
        <h5 class="fw-bold mb-0">Your Order ({{ $cartItems->count() }} items)</h5>
      </div>
      <div class="card-body p-0">

        {{-- Items list --}}
        @foreach($cartItems as $item)
        <div class="d-flex align-items-center gap-3 p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
          <div class="position-relative">
            @if($item->product->images && count($item->product->images))
              <img src="{{ asset('storage/' . $item->product->images[0]) }}"
                   alt="{{ $item->product->name }}" class="order-item-img" />
            @else
              <img src="{{ Vite::asset('resources/images/pages/puma-shoes.jpeg') }}"
                   alt="{{ $item->product->name }}" class="order-item-img" />
            @endif
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
              {{ $item->quantity }}
            </span>
          </div>
          <div class="flex-grow-1">
            <div class="fw-semibold small">{{ Str::limit($item->product->name, 35) }}</div>
            <div class="text-muted small">${{ number_format($item->product->price, 2) }} × {{ $item->quantity }}</div>
          </div>
          <div class="fw-bold">${{ number_format($item->product->price * $item->quantity, 2) }}</div>
        </div>
        @endforeach

        {{-- Totals --}}
        @php
          $subtotal = $cartItems->sum(fn($i) => $i->product->price * $i->quantity);
          $shipping = $subtotal >= 50 ? 0 : 9.99;
          $tax = $subtotal * 0.08;
          $total = $subtotal + $shipping + $tax;
        @endphp
        <div class="p-4 border-top">
          <div class="d-flex justify-content-between text-muted mb-2">
            <span>Subtotal</span><span>${{ number_format($subtotal, 2) }}</span>
          </div>
          <div class="d-flex justify-content-between text-muted mb-2">
            <span>Shipping</span>
            <span class="{{ $shipping == 0 ? 'text-success fw-semibold' : '' }}">
              {{ $shipping == 0 ? 'FREE' : '$' . number_format($shipping, 2) }}
            </span>
          </div>
          <div class="d-flex justify-content-between text-muted mb-3">
            <span>Tax (8%)</span><span>${{ number_format($tax, 2) }}</span>
          </div>
          <div class="d-flex justify-content-between fw-bold fs-5 border-top pt-3">
            <span>Total</span>
            <span class="text-primary">${{ number_format($total, 2) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
  // Show/hide card fields based on payment method
  document.querySelectorAll('[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', function() {
      document.getElementById('card-fields').style.display =
        this.value === 'credit_card' ? 'block' : 'none';
    });
  });

  // Format card number with spaces
  document.querySelector('[name="card_number"]')?.addEventListener('input', function() {
    this.value = this.value.replace(/\s/g, '').replace(/(.{4})/g, '$1 ').trim();
  });
</script>
@endsection
