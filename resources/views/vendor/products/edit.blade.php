@extends('layouts/layoutMaster')

@section('title', 'Edit Product')

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
  <a href="{{ route('vendor.products.index') }}" class="btn btn-icon btn-outline-secondary">
    <i class="ti ti-arrow-left"></i>
  </a>
  <div>
    <h4 class="fw-bold mb-0">Edit Product: {{ $product->name }}</h4>
    <p class="text-muted mb-0 small">Update catalog information, pricing or inventory</p>
  </div>
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

<form action="{{ route('vendor.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
  @csrf
  @method('PUT')

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header border-0">
          <h6 class="fw-bold mb-0">Product Details</h6>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">Product Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required />
          </div>

          <div class="mb-3">
            <label class="form-label">Category <span class="text-danger">*</span></label>
            <select name="category_id" class="form-select" required>
              <option value="">Select a Category</option>
              @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                  {{ $category->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Description <span class="text-danger">*</span></label>
            <textarea name="description" class="form-control" rows="5" required>{{ old('description', $product->description) }}</textarea>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header border-0">
          <h6 class="fw-bold mb-0">Pricing & Stock</h6>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-sm-4">
              <label class="form-label">Cost Price ($) <span class="text-danger">*</span></label>
              <input type="number" step="0.01" name="cost_price" class="form-control" value="{{ old('cost_price', $product->cost_price) }}" required />
            </div>
            <div class="col-sm-4">
              <label class="form-label">Selling Price ($) <span class="text-danger">*</span></label>
              <input type="number" step="0.01" name="selling_price" class="form-control" value="{{ old('selling_price', $product->selling_price) }}" required />
            </div>
            <div class="col-sm-4">
              <label class="form-label">Stock Quantity <span class="text-danger">*</span></label>
              <input type="number" name="stock" class="form-control" value="{{ old('stock', $product->stock) }}" min="0" required />
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header border-0">
          <h6 class="fw-bold mb-0">Product Image</h6>
        </div>
        <div class="card-body text-center">
          <div class="p-3 border-2 border-dashed rounded mb-3 bg-body-tertiary">
            @if($product->image)
              <img id="preview-img" src="{{ asset('storage/' . $product->image) }}"
                   style="max-width: 100%; height: 160px; object-fit: contain; border-radius: .5rem;" />
            @else
              <img id="preview-img" src="{{ Vite::asset('resources/images/pages/puma-shoes.jpeg') }}"
                   style="max-width: 100%; height: 160px; object-fit: contain; border-radius: .5rem;" />
            @endif
          </div>
          <input type="file" name="image" id="image-input" class="form-control form-control-sm" accept="image/*" onchange="previewFile(this)" />
          <small class="text-muted d-block mt-2">Upload new file to replace current image</small>
        </div>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <button type="submit" class="btn btn-primary w-100 mb-2">
            <i class="ti ti-check me-1"></i> Update Product
          </button>
          <a href="{{ route('vendor.products.index') }}" class="btn btn-outline-secondary w-100">Cancel</a>
        </div>
      </div>
    </div>
  </div>
</form>

@endsection

@section('page-script')
<script>
function previewFile(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => document.getElementById('preview-img').src = e.target.result;
    reader.readAsDataURL(input.files[0]);
  }
}
</script>
@endsection
