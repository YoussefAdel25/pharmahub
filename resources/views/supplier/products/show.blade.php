@extends('layouts.app')

@php
    $key = $product->supplier_id . '-' . $product->id;
    $discountRate = isset($discounts[$key]) ? $discounts[$key]->discount_rate : 0;
    $priceAfterDiscount = $discountRate > 0 ? $product->price * (1 - $discountRate / 100) : $product->price;
@endphp

@section('content')
    <div class="container py-5">

        <!-- Admin Action Buttons -->
        @if (auth()->user()->role != 'customer')
            <div class="position-absolute top-0 end-0 mt-3 me-3 d-flex gap-2">
                <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">Edit</a>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                    data-bs-target="#deleteModal">Delete</button>
            </div>
        @endif

        <div class="row g-4">
            <!-- Product Image -->
            <div class="col-md-6">
                <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/500x500?text=No+Image' }}"
                    class="img-fluid rounded shadow-sm" alt="{{ $product->name }}">
            </div>

            <!-- Product Details -->
            <div class="col-md-6">
                <h1 class="fw-bold">{{ $product->name }}</h1>
                <p class="text-muted mb-2">{{ Str::limit($product->description, 200) }}</p>

                <!-- Price -->
                <div class="mb-3">
                    @if ($discountRate > 0)
                        <span
                            class="text-decoration-line-through text-muted">${{ number_format($product->price, 2) }}</span>
                        <span class="text-success fw-bold h4 ms-2">${{ number_format($priceAfterDiscount, 2) }}</span>
                        <span class="badge bg-success ms-2">{{ $discountRate }}% Off</span>
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

                <!-- Full Description -->
                <p class="mb-4"><strong>Description:</strong> {{ $product->description }}</p>

                <!-- Customer Actions -->
                @if (auth()->user()->role === 'customer')
                    <div class="d-flex flex-column flex-sm-row gap-2 mb-3">
                        <div class="cart-controls d-flex justify-content-center align-items-center gap-2"
                            data-product-id="{{ $product->id }}" data-price="{{ $priceAfterDiscount }}">
                            <button class="btn btn-outline-primary btn-lg add-to-cart-btn">
                                Add to Cart
                            </button>
                            <div class="quantity-controls d-none align-items-center gap-2">
                                <button class="btn btn-outline-secondary btn-sm px-2 py-1 decrease">-</button>
                                <span class="quantity small">1</span>
                                <button class="btn btn-outline-secondary btn-sm px-2 py-1 increase">+</button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tabs: Description / Specifications -->
        <div class="row mt-5">
            <div class="col-12">
                <ul class="nav nav-tabs" id="productTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="description-tab" data-bs-toggle="tab"
                            data-bs-target="#description" type="button">Description</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs"
                            type="button">Specifications</button>
                    </li>
                </ul>
                <div class="tab-content p-4 border border-top-0 rounded-bottom shadow-sm">
                    <div class="tab-pane fade show active" id="description">
                        <p>{{ $product->description }}</p>
                    </div>
                    <div class="tab-pane fade" id="specs">
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
                                    ${{ number_format($priceAfterDiscount, 2) }}</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete <strong>{{ $product->name }}</strong>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form action="{{ route('supplier.products.destroy', $product) }}" method="POST">
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


@section('js')
    <script>
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var cartModal = new bootstrap.Modal(document.getElementById('cartModal'));

            function showToast(message) {
                $('#cart-toast .alert').text(message).fadeIn();
                setTimeout(() => {
                    $('#cart-toast').fadeOut();
                }, 2000);
            }

            function loadCart() {
                $.get('/cart/items', function(response) {
                    $('#cart-items').html(response.html);
                    $('#cart-total').text(response.total);
                    $('#cart-count').text(response.count);
                });
            }

            loadCart();

            $('#cart-icon').on('click', function(e) {
                e.preventDefault();
                cartModal.show();
                loadCart();
            });

            // Add to cart
            $(document).on('click', '.add-to-cart-btn', function() {
                let container = $(this).closest('.cart-controls');
                let productId = container.data('product-id');
                let price = container.data('price');

                $.post('/cart/add', {
                    product_id: productId,
                    quantity: 1,
                    price: price
                }, function(response) {
                    if (response.success) {
                        container.find('.add-to-cart-btn').addClass('d-none');
                        container.find('.quantity-controls').removeClass('d-none');
                        container.find('.quantity').text(response.quantity);
                        $('#cart-count').text(response.count);
                        loadCart();
                        showToast('Product added to the basket successfully!');
                    }
                });
            });

            // Increase quantity
            $(document).on('click', '.increase', function() {
                let container = $(this).closest('.cart-controls');
                let qtySpan = container.find('.quantity');
                let qty = parseInt(qtySpan.text(), 10);
                qty += 1;
                qtySpan.text(qty);

                let productId = container.data('product-id');
                let price = container.data('price');

                $.post('/cart/update', {
                    product_id: productId,
                    quantity: qty,
                    price: price
                }, function(response) {
                    if (response.success) {
                        loadCart();
                    } else {
                        showToast(response.message);

                        if (response.alternative) {
                            let alt = response.alternative;
                            Swal.fire({
                                title: "Quota exceeded!",
                                text: "We recommend trying this alternative product:",
                                imageUrl: alt.image,
                                imageHeight: 150,
                                showCancelButton: true,
                                confirmButtonText: "View Alternative",
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "/products/" + alt.id;
                                }
                            });
                        }
                    }
                });
            });

            // Decrease quantity
            $(document).on('click', '.decrease', function() {
                let container = $(this).closest('.cart-controls');
                let qtySpan = container.find('.quantity');
                let qty = parseInt(qtySpan.text(), 10);
                if (qty <= 1) return;
                qty -= 1;
                qtySpan.text(qty);

                let productId = container.data('product-id');
                let price = container.data('price');

                $.post('/cart/update', {
                    product_id: productId,
                    quantity: qty,
                    price: price
                }, function(response) {
                    if (response.success) loadCart();
                });
            });
        });
    </script>
@endsection
