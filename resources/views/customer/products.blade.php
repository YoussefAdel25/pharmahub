@extends('layouts.app')

@section('content')
    <div class="container py-4">

        @if ($recommendedProducts->isNotEmpty())
            <h3 class="mb-3 text-primary">Recommended Products</h3>
            <div class="row g-4 mb-5">
                @foreach ($recommendedProducts as $product)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm border border-primary recommended-card">
                            <a href="{{ route('products.showProduct', $product) }}" class="text-decoration-none text-dark">
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
                                        $priceAfterDiscount =
                                            $discountRate > 0
                                                ? $product->price * (1 - $discountRate / 100)
                                                : $product->price;
                                    @endphp

                                    <div class="mb-2">
                                        <span class="fw-bold">Price: </span>
                                        @if ($discountRate > 0)
                                            <span class="text-decoration-line-through">${{ $product->price }}</span>
                                            <span
                                                class="text-success fw-bold">${{ number_format($priceAfterDiscount, 2) }}</span>
                                        @else
                                            <span class="fw-bold">${{ $product->price }}</span>
                                        @endif
                                    </div>

                                    <div class="mb-2">
                                        <span class="badge bg-primary">{{ ucfirst($product->type ?? 'N/A') }}</span>
                                        <span class="badge bg-secondary">Quota: {{ $product->quota_period ?? 'N/A' }}</span>
                                    </div>
                                    <span class="text-muted">Supplier: {{ $product->supplier->name }}</span>

                                </div>
                            </a>
                            <div class="card-footer text-center bg-white border-top">
                                <div class="cart-controls d-flex justify-content-center align-items-center gap-2"
                                    data-product-id="{{ $product->id }}" data-price="{{ $priceAfterDiscount }}">
                                    <button class="btn btn-sm btn-success add-to-cart-btn">Add to Cart</button>

                                    {{-- Quantity Controls (hidden initially) --}}
                                    <div class="quantity-controls d-none align-items-center gap-1">
                                        <button class="btn btn-sm btn-outline-secondary decrease">-</button>
                                        <span class="quantity">1</span>
                                        <button class="btn btn-sm btn-outline-secondary increase">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

{{-- Other Products --}}
<h3 class="mb-3 text-secondary">Other Products</h3>
<div class="row g-4">
    @foreach ($allProducts as $product)
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm other-card">
                <a href="{{ route('products.showProduct', $product) }}" class="text-decoration-none text-dark">
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
                            $priceAfterDiscount = $discountRate > 0
                                ? $product->price * (1 - $discountRate / 100)
                                : $product->price;
                        @endphp

                        <div class="mb-2">
                            <span class="fw-bold">Price: </span>
                            @if ($discountRate > 0)
                                <span class="text-decoration-line-through">${{ $product->price }}</span>
                                <span class="text-success fw-bold">${{ number_format($priceAfterDiscount, 2) }}</span>
                            @else
                                <span class="fw-bold">${{ $product->price }}</span>
                            @endif
                        </div>

                        <div class="mb-2">
                            <span class="badge bg-primary">{{ ucfirst($product->type ?? 'N/A') }}</span>
                            <span class="badge bg-secondary">Quota: {{ $product->quota_period ?? 'N/A' }}</span>
                        </div>
                        <span class="text-muted">Supplier: {{ $product->supplier->name }}</span>
                    </div>
                </a>

                <div class="card-footer text-center bg-white border-top">
                    <div class="cart-controls d-flex justify-content-center align-items-center gap-2"
                        data-product-id="{{ $product->id }}" data-price="{{ $priceAfterDiscount }}">
                        <button class="btn btn-sm btn-success add-to-cart-btn">Add to Cart</button>

                        <div class="quantity-controls d-none align-items-center gap-1">
                            <button class="btn btn-sm btn-outline-secondary decrease">-</button>
                            <span class="quantity">1</span>
                            <button class="btn btn-sm btn-outline-secondary increase">+</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endforeach
</div>


    </div>

@endsection

@section('css')
    <style>
        .recommended-card {
            border-width: 2px !important;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .recommended-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }

        .other-card {
            transition: transform 0.25s, box-shadow 0.25s;
        }

        .other-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
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
                    if (response.success) loadCart();
                });
            });

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
