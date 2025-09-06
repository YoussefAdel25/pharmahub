@extends('layouts.app')
<style>
    .product-actions {
        position: absolute;
        top: 8px;
        right: 8px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease-in-out;
    }

    .product-card:hover .product-actions {
        opacity: 1;
        pointer-events: auto;
    }

    .product-action-icon {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: transparent;
        color: inherit;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        border: none;
        cursor: pointer;
        transition: transform 0.2s ease-in-out;
    }

    .product-action-icon:hover {
        transform: scale(1.2);
    }
</style>
@section('content')
    <div class="container py-4">
        <div id="alertContainer"></div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Products</h1>
            <a href="{{ route('supplier.products.create') }}" class="btn btn-primary">
                + Add Product
            </a>
        </div>

        <div class="row g-4">
            @foreach ($products as $product)
                <div class="col-md-4" id="productCard{{ $product->id }}">

                    <div class="card h-100 shadow-sm position-relative product-card">

                        <div class="product-actions">
                            <a href="{{ route('supplier.products.edit', $product) }}" class="product-action-icon"
                                title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <button type="button" class="product-action-icon delete-btn" data-id="{{ $product->id }}"
                                data-name="{{ $product->name }}" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>

                        <a href="{{ route('supplier.products.show', $product) }}" class="text-decoration-none text-dark">
                            @if ($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top"
                                    alt="{{ $product->name }}" style="height: 220px; object-fit: cover;">
                            @endif

                            <div class="card-body">
                                <h5 class="card-title">{{ $product->name }}</h5>
                                <p class="card-text text-muted">{{ Str::limit($product->description, 60) }}</p>

                                @php
                                    $key = $product->supplier_id . '-' . $product->id;
                                    $discountRate = isset($discounts[$key]) ? $discounts[$key]->discount_rate : 0;
                                    $discountedPrice = $product->price * (1 - $discountRate / 100);
                                @endphp

                                <div class="mb-2">
                                    <span class="fw-bold">Price: </span>
                                    @if ($discountRate > 0)
                                        <span class="text-decoration-line-through">${{ $product->price }}</span>
                                        <span class="text-success fw-bold">${{ number_format($discountedPrice, 2) }}</span>
                                    @else
                                        <span class="fw-bold">${{ $product->price }}</span>
                                    @endif
                                </div>

                                <div class="mb-2">
                                    <span class="badge bg-primary">{{ ucfirst($product->type ?? 'N/A') }}</span>
                                    <span class="badge bg-secondary">
                                        Quota:
                                        {{ $product->quota_period ? ucfirst(str_replace('_', ' ', $product->quota_period)) : 'N/A' }}
                                    </span>
                                </div>

                                <small class="text-muted">Supplier: {{ $product->supplier->name }}</small>
                            </div>
                        </a>

                        {{--  <div class="card-footer text-center">
                                <a href="{{ route('orders.create', ['product' => $product->id]) }}"
                                    class="btn btn-success w-100">
                                    Order Now
                                </a>
                            </div>  --}}

                    </div>
                </div>
            @endforeach
        </div>
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete <strong id="deleteProductName"></strong>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <style>
        .product-card:hover {
            transform: translateY(-5px);
            transition: all 0.3s ease-in-out;
            cursor: pointer;
        }

        .card-body a {
            color: inherit;
        }
    </style>
@endsection
@section('js')
    <script>
        let deleteProductId = null;

        $(document).ready(function() {
            $('.delete-btn').click(function() {
                deleteProductId = $(this).data('id');
                let productName = $(this).data('name');
                $('#deleteProductName').text(productName);
                var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                deleteModal.show();
            });

            $('#confirmDeleteBtn').click(function() {
                if (!deleteProductId) return;

                $.ajax({
                    url: '/supplier/products/delete/' + deleteProductId,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#deleteModal').modal('hide');

                        $('#productCard' + deleteProductId).remove();
                        deleteProductId = null;

                        let alertHtml = `
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            ${response.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
                        $('#alertContainer').html(alertHtml);

                        setTimeout(() => {
                            $('.alert').alert('close');
                        }, 3000);
                    },
                    error: function(xhr) {
                        alert('Something went wrong!');
                    }
                });
            });
        });
    </script>
@endsection
