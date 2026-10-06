@extends('layouts/layoutMaster')

@section('title', isset($product) ? 'Edit Product' : 'Add New Product')

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
  <a href="{{ route('vendor.products.index') }}" class="btn btn-icon btn-outline-secondary">
    <i class="ti ti-arrow-left"></i>
  </a>
  <div>
    <h4 class="fw-bold mb-0">{{ isset($product) ? 'Edit Product' : 'Add New Product' }}</h4>
    <p class="text-muted mb-0 small">{{ isset($product) ? 'Update your product details' : 'Fill in the details for your new product' }}</p>
  </div>
</div>

@if($errors->any())
  <div class="alert alert-danger mb-4">
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
@endif

<form action="{{ isset($product) ? route('vendor.products.update', $product) : route('vendor.products.store') }}"
      method="POST" enctype="multipart/form-data">
  @csrf
  @if(isset($product)) @method('PUT') @endif

  <div class="row g-4">

    {{-- Left: Main Details --}}
    <div class="col-lg-8">

      {{-- Basic Info --}}
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header border-0">
          <h6 class="fw-bold mb-0">Product Information</h6>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">Product Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $product->name ?? '') }}"
                   placeholder="Enter product name" required />
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Description <span class="text-danger">*</span></label>
            <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                      rows="5" placeholder="Describe your product in detail…" required>{{ old('description', $product->description ?? '') }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="row g-3">
            <div class="col-sm-6">
              <label class="form-label">Category</label>
              <select name="category" class="form-select">
                <option value="">Select Category</option>
                @foreach(['Electronics','Fashion','Home & Garden','Sports','Beauty','Books','Food','Other'] as $cat)
                  <option value="{{ $cat }}" {{ old('category', $product->category ?? '') == $cat ? 'selected' : '' }}>
                    {{ $cat }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-sm-6">
              <label class="form-label">SKU</label>
              <input type="text" name="sku" class="form-control"
                     value="{{ old('sku', $product->sku ?? '') }}"
                     placeholder="Product SKU" />
            </div>
          </div>
        </div>
      </div>

      {{-- Pricing & Stock --}}
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header border-0">
          <h6 class="fw-bold mb-0">
            <img src="{{ Vite::asset('resources/images/svg/Wallet.svg') }}" style="width:18px;" class="me-2" />
            Pricing & Inventory
          </h6>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-sm-6">
              <label class="form-label">Price <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                       step="0.01" min="0"
                       value="{{ old('price', $product->price ?? '') }}"
                       placeholder="0.00" required />
                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-sm-6">
              <label class="form-label">Original Price <span class="text-muted small">(for discount)</span></label>
              <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" name="original_price" class="form-control"
                       step="0.01" min="0"
                       value="{{ old('original_price', $product->original_price ?? '') }}"
                       placeholder="0.00" />
              </div>
            </div>
            <div class="col-sm-6">
              <label class="form-label">Stock Quantity <span class="text-danger">*</span></label>
              <input type="number" name="stock_quantity" class="form-control @error('stock_quantity') is-invalid @enderror"
                     min="0"
                     value="{{ old('stock_quantity', $product->stock_quantity ?? '') }}"
                     placeholder="0" required />
              @error('stock_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-sm-6">
              <label class="form-label">Status</label>
              <select name="is_active" class="form-select">
                <option value="1" {{ old('is_active', $product->is_active ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('is_active', $product->is_active ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
              </select>
            </div>
          </div>
        </div>
      </div>

    </div>

    {{-- Right: Images --}}
    <div class="col-lg-4">

      {{-- Product Images --}}
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header border-0">
          <h6 class="fw-bold mb-0">Product Images</h6>
        </div>
        <div class="card-body">

          {{-- Current images preview --}}
          @if(isset($product) && $product->images && count($product->images))
          <div class="mb-3">
            <label class="form-label text-muted small">Current Images</label>
            <div class="d-flex flex-wrap gap-2">
              @foreach($product->images as $img)
              <div class="position-relative">
                <img src="{{ asset('storage/' . $img) }}"
                     style="width:70px;height:70px;object-fit:cover;border-radius:.375rem;" />
              </div>
              @endforeach
            </div>
          </div>
          @else
          {{-- Placeholder preview from resources/images --}}
          <div class="mb-3 text-center p-4 bg-body-tertiary rounded border-2 border-dashed">
            <img src="{{ Vite::asset('resources/images/pages/puma-shoes.jpeg') }}"
                 style="width:100%;height:140px;object-fit:cover;border-radius:.5rem;opacity:.5;" />
            <div class="text-muted small mt-2">No images uploaded yet</div>
          </div>
          @endif

          <label class="form-label">Upload Images</label>
          <div class="border-2 border-dashed rounded p-4 text-center" id="upload-area"
               style="cursor:pointer;border-color:var(--bs-border-color);"
               onclick="document.getElementById('product-images').click()">
            <img src="{{ Vite::asset('resources/images/svg/rocket.svg') }}"
                 style="width:36px;opacity:.5;" alt="" class="mb-2" />
            <div class="text-muted small">Click to upload or drag & drop</div>
            <div class="text-muted" style="font-size:.7rem;">PNG, JPG, WebP up to 2MB each</div>
          </div>
          <input type="file" name="images[]" id="product-images"
                 class="d-none" accept="image/*" multiple
                 onchange="previewImages(this)" />

          {{-- Preview container --}}
          <div id="image-preview" class="d-flex flex-wrap gap-2 mt-3"></div>
        </div>
      </div>

      {{-- Tips --}}
      <div class="card border-0 bg-label-info">
        <div class="card-body p-3">
          <h6 class="fw-semibold mb-2">
            <img src="{{ Vite::asset('resources/images/svg/lightbulb.svg') }}"
                 style="width:18px;" class="me-1" /> Tips for Great Listings
          </h6>
          <ul class="mb-0 small text-muted ps-3">
            <li>Use high-quality images (min 800×800px)</li>
            <li>Write a detailed, honest description</li>
            <li>Set competitive pricing</li>
            <li>Keep stock quantities updated</li>
          </ul>
        </div>
      </div>

    </div>

    {{-- Submit buttons --}}
    <div class="col-12">
      <div class="d-flex gap-3">
        <button type="submit" class="btn btn-primary px-5">
          <i class="ti ti-{{ isset($product) ? 'device-floppy' : 'plus' }} me-2"></i>
          {{ isset($product) ? 'Update Product' : 'Add Product' }}
        </button>
        <a href="{{ route('vendor.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </div>
  </div>
</form>

@endsection

@section('page-script')
<script>
  function previewImages(input) {
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';
    Array.from(input.files).forEach(file => {
      const reader = new FileReader();
      reader.onload = e => {
        const img = document.createElement('img');
        img.src = e.target.result;
        img.style.cssText = 'width:70px;height:70px;object-fit:cover;border-radius:.375rem;';
        preview.appendChild(img);
      };
      reader.readAsDataURL(file);
    });
  }

  // Drag & drop highlight
  const uploadArea = document.getElementById('upload-area');
  uploadArea?.addEventListener('dragover', e => { e.preventDefault(); uploadArea.style.borderColor = 'var(--bs-primary)'; });
  uploadArea?.addEventListener('dragleave', () => { uploadArea.style.borderColor = 'var(--bs-border-color)'; });
  uploadArea?.addEventListener('drop', e => {
    e.preventDefault();
    uploadArea.style.borderColor = 'var(--bs-border-color)';
    const input = document.getElementById('product-images');
    input.files = e.dataTransfer.files;
    previewImages(input);
  });
</script>
@endsection
