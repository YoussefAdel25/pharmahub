@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Add Product</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                @csrf

                <!-- Name -->
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input name="name" class="form-control" required>
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>

                <!-- Price -->
                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input name="price" type="number" step="0.01" class="form-control" required>
                </div>

                <!-- Discount -->
                <div class="mb-3">
                    <label class="form-label">Discount (%)</label>
                    <input name="discount" type="number" step="0.01" class="form-control" min="0" max="100" value="0">
                    <small class="text-muted">Enter discount percentage for this product (0-100)</small>
                </div>

                <!-- Stock -->
                <div class="mb-3">
                    <label class="form-label">Stock</label>
                    <input name="stock" type="number" class="form-control" value="0" required>
                </div>

                <!-- Quota Limit -->
                <div class="mb-3">
                    <label class="form-label">Quota Limit</label>
                    <input name="quota_limit" type="number" class="form-control">
                    <small class="text-muted">Maximum allowed quantity per selected quota period</small>
                </div>

                <!-- Quota Type -->
                <div class="mb-3">
                    <label class="form-label">Quota Type</label>
                    <select name="quota_period" class="form-select">
                        <option value="">Select Quota Type</option>
                        <option value="per_order">Per Order</option>
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                        <option value="per_customer">Per Customer</option>
                    </select>
                    <small class="text-muted">Select the period for quota limitation</small>
                </div>

                <!-- Product Image -->
                <div class="mb-3">
                    <label class="form-label">Product Image</label>
                    <input type="file" name="image" class="form-control">
                </div>

                <!-- Submit Button -->
                <button class="btn btn-primary w-100">Save Product</button>
            </form>
        </div>
    </div>
</div>
@endsection
