@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Add Product</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('supplier.products.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    <!-- Name -->
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Stock -->
                    <div class="col-md-3">
                        <label class="form-label">Stock</label>
                        <input name="stock" type="number"
                               class="form-control @error('stock') is-invalid @enderror"
                               value="{{ old('stock', 0) }}" required>
                        @error('stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Price -->
                    <div class="col-md-3">
                        <label class="form-label">Price</label>
                        <input name="price" type="number" step="0.01"
                               class="form-control @error('price') is-invalid @enderror"
                               value="{{ old('price') }}" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mt-3">
                    <!-- Discount -->
                    <div class="col-md-3">
                        <label class="form-label">Discount (%)</label>
                        <input name="discount" type="number" step="0.01" min="0" max="100"
                               class="form-control @error('discount') is-invalid @enderror"
                               value="{{ old('discount', 0) }}">
                        <small class="text-muted">0-100%</small>
                        @error('discount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Quota Limit -->
                    <div class="col-md-3">
                        <label class="form-label">Quota Limit</label>
                        <input name="quota_limit" type="number"
                               class="form-control @error('quota_limit') is-invalid @enderror"
                               value="{{ old('quota_limit') }}">
                        @error('quota_limit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Quota Type -->
                    <div class="col-md-6">
                        <label class="form-label">Quota Type</label>
                        <select name="quota_period"
                                class="form-select @error('quota_period') is-invalid @enderror">
                            <option value="">Select Quota Type</option>
                            <option value="per_order" {{ old('quota_period') == 'per_order' ? 'selected' : '' }}>Per Order</option>
                            <option value="daily" {{ old('quota_period') == 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ old('quota_period') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="monthly" {{ old('quota_period') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="per_customer" {{ old('quota_period') == 'per_customer' ? 'selected' : '' }}>Per Customer</option>
                        </select>
                        @error('quota_period')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mt-3">
                    <!-- Product Type -->
                    <div class="col-md-6">
                        <label class="form-label">Product Type</label>
                        <select name="type"
                                class="form-select @error('type') is-invalid @enderror">
                            <option value="">Select Type</option>
                            <option value="package" {{ old('type') == 'package' ? 'selected' : '' }}>Package</option>
                            <option value="kit" {{ old('type') == 'kit' ? 'selected' : '' }}>Kit</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Product Image -->
                    <div class="col-md-6">
                        <label class="form-label">Product Image</label>
                        <input type="file" name="image"
                               class="form-control @error('image') is-invalid @enderror">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="row mt-3">
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description"
                                  class="form-control @error('description') is-invalid @enderror"
                                  rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="row mt-4">
                    <div class="col-12">
                        <button class="btn btn-primary w-100">Save Product</button>
                    </div>
                </div>
            </form>

            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success mt-3">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
