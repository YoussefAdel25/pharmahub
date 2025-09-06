@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Edit Product</h1>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Back to Products</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('supplier.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <!-- Name -->
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $product->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Stock -->
                    <div class="col-md-3">
                        <label class="form-label">Stock</label>
                        <input name="stock" type="number" class="form-control @error('stock') is-invalid @enderror"
                               value="{{ old('stock', $product->stock) }}" required>
                        @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Price -->
                    <div class="col-md-3">
                        <label class="form-label">Price</label>
                        <input name="price" type="number" step="0.01" class="form-control @error('price') is-invalid @enderror"
                               value="{{ old('price', $product->price) }}" required>
                        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row g-3 mt-3">
                    <!-- Discount -->
                    <div class="col-md-3">
                        <label class="form-label">Discount (%)</label>
                        <input name="discount" type="number" step="0.01" min="0" max="100"
                               class="form-control @error('discount') is-invalid @enderror"
                               value="{{ old('discount', $discount) }}">
                        <small class="text-muted">0-100%</small>
                        @error('discount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Quota Limit -->
                    <div class="col-md-3">
                        <label class="form-label">Quota Limit</label>
                        <input name="quota_limit" type="number"
                               class="form-control @error('quota_limit') is-invalid @enderror"
                               value="{{ old('quota_limit', $product->quota_limit) }}">
                        @error('quota_limit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Quota Type -->
                    <div class="col-md-6">
                        <label class="form-label">Quota Type</label>
                        <select name="quota_period" class="form-select @error('quota_period') is-invalid @enderror">
                            <option value="">Select Quota Type</option>
                            <option value="per_order" {{ old('quota_period', $product->quota_period) == 'per_order' ? 'selected' : '' }}>Per Order</option>
                            <option value="daily" {{ old('quota_period', $product->quota_period) == 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ old('quota_period', $product->quota_period) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="monthly" {{ old('quota_period', $product->quota_period) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="per_customer" {{ old('quota_period', $product->quota_period) == 'per_customer' ? 'selected' : '' }}>Per Customer</option>
                        </select>
                        @error('quota_period') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row g-3 mt-3">
                    <!-- Product Type -->
                    <div class="col-md-6">
                        <label class="form-label">Product Type</label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror">
                            <option value="">Select Type</option>
                            <option value="package" {{ old('type', $product->type) == 'package' ? 'selected' : '' }}>Package</option>
                            <option value="kit" {{ old('type', $product->type) == 'kit' ? 'selected' : '' }}>Kit</option>
                        </select>
                        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Product Image -->
                    <div class="col-md-6">
                        <label class="form-label">Product Image</label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
                        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid mt-2" style="max-height: 150px;">
                        @endif
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-3 mt-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="4"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100">Update Product</button>

                @if(session('success'))
                    <div class="alert alert-success mt-3">{{ session('success') }}</div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection
