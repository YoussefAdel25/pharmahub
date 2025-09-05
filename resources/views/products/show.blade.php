@extends('layouts.app')

@section('content')
    <div class="container py-5 position-relative">


        @if (auth()->user()->role != 'customer')
            <!-- Action Buttons: Edit/Delete -->
            <div class="position-absolute top-0 end-0 mt-3 me-3 d-flex gap-2">
                <!-- Edit Button -->
                <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> Edit
                </a>

                <!-- Delete Button (Open Modal) -->
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </div>
        @endif

        <div class="row">
            <!-- Product Image -->
            <div class="col-md-6">
                @if ($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded shadow-sm"
                        alt="{{ $product->name }}">
                @else
                    <img src="https://via.placeholder.com/500x500?text=No+Image" class="img-fluid rounded shadow-sm"
                        alt="No Image">
                @endif
            </div>

            <!-- Product Details -->
            <div class="col-md-6">
                <h1 class="fw-bold">{{ $product->name }}</h1>
                <p class="text-muted mb-2">{{ Str::limit($product->description, 200) }}</p>

                @php
                    $discountRate = $discount ?? 0;
                    $discountedPrice = $product->price * (1 - $discountRate / 100);
                @endphp

                <!-- Price & Discount -->
                <div class="mb-3">
                    <span class="fw-bold">Price: </span>
                    @if ($discountRate > 0)
                        <span
                            class="text-decoration-line-through text-muted">${{ number_format($product->price, 2) }}</span>
                        <span class="text-success fw-bold h4">${{ number_format($discountedPrice, 2) }}</span>
                        <span class="badge bg-success">{{ $discountRate }}% Off</span>
                    @else
                        <span class="fw-bold h4">${{ number_format($product->price, 2) }}</span>
                    @endif
                </div>

                <!-- Badges -->
                <div class="mb-3">
                    <span class="badge bg-primary">{{ ucfirst($product->type ?? 'N/A') }}</span>
                    <span class="badge bg-secondary">
                        Quota:
                        {{ $product->quota_period ? ucfirst(str_replace('_', ' ', $product->quota_period)) : 'N/A' }}
                    </span>
                    <span class="badge bg-info text-dark">Supplier: {{ $product->supplier->name }}</span>
                </div>

                <!-- Description -->
                <p class="mb-4"><strong>Description:</strong> {{ $product->description }}</p>

                <!-- Action Buttons for Customer -->
                @can('isCustomer')
                    <div class="d-flex gap-2">
                        <a href="{{ route('orders.create', ['product' => $product->id]) }}" class="btn btn-success btn-lg">
                            Order Now
                        </a>
                        <button class="btn btn-outline-primary btn-lg">
                            <i class="bi bi-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                @endcan
            </div>
        </div>

        <!-- Optional: Tabs -->
        <div class="row mt-5">
            <div class="col-12">
                <ul class="nav nav-tabs" id="productTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="description-tab" data-bs-toggle="tab"
                            data-bs-target="#description" type="button" role="tab">Description</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs" type="button"
                            role="tab">Specifications</button>
                    </li>
                </ul>
                <div class="tab-content p-4 border border-top-0 rounded-bottom shadow-sm" id="productTabContent">
                    <div class="tab-pane fade show active" id="description" role="tabpanel">
                        <p>{{ $product->description }}</p>
                    </div>
                    <div class="tab-pane fade" id="specs" role="tabpanel">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Type:</strong> {{ ucfirst($product->type ?? 'N/A') }}</li>
                            <li class="list-group-item"><strong>Quota Period:</strong>
                                {{ $product->quota_period ?? 'N/A' }}</li>
                            <li class="list-group-item"><strong>Supplier:</strong> {{ $product->supplier->name }}</li>
                            <li class="list-group-item"><strong>Price:</strong> ${{ number_format($product->price, 2) }}
                            </li>
                            @if ($discountRate > 0)
                                <li class="list-group-item"><strong>Discount:</strong> {{ $discountRate }}%</li>
                                <li class="list-group-item"><strong>Discounted Price:</strong>
                                    ${{ number_format($discountedPrice, 2) }}</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete <strong>{{ $product->name }}</strong>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form action="{{ route('products.destroy', $product) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Yes, Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
