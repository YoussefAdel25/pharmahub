@extends('layouts.app')



@section('css')
<style>
    .product-card {
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .product-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
    }

    .product-card .badge {
        padding: 0.25em 0.6em;
        font-weight: 500;
        font-size: 0.65rem;
    }

    .card-body {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 0.5rem;
        min-height: 180px;
    }

    .card-body h6 {
        min-height: 36px;
        margin-bottom: 0.3rem;
    }

    .card-body p {
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 0.3rem;
        min-height: 40px;
    }

    .card-footer .btn-success {
        transition: all 0.2s ease;
    }

    .card-footer .btn-success:hover {
        background-color: #198754;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        border-left: 4px solid;
        padding-left: 0.75rem;
        margin-bottom: 1rem;
    }

    .section-title-primary { color: #0d6efd; border-color: #0d6efd; }
    .section-title-secondary { color: #6c757d; border-color: #6c757d; }

    .section-title i { font-size: 1.25rem; margin-right: 0.5rem; }
</style>
@endsection

@section('content')
<div class="container py-4">

    {{-- Recommended Products --}}
    @if(isset($recommendedProducts) && $recommendedProducts->isNotEmpty())
        <h3 class="section-title section-title-primary">
            <i class="bi bi-star-fill"></i> Recommended Products
        </h3>
        <div class="row g-3 mb-5">
            @foreach($recommendedProducts as $product)
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="card h-100 shadow-sm product-card">
                        <a href="{{ route('products.showProduct', $product) }}" class="text-decoration-none text-dark">
                            <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/200x150?text=No+Image' }}"
                                class="card-img-top" alt="{{ $product->name }}" style="height: 150px; object-fit: cover;">

                            <div class="card-body">
                                <span class="badge rounded-pill" style="background-color: {{ $product->type ? '#20c997' : '#6c757d' }}; color: #fff;">
                                    {{ $product->type ? ucfirst($product->type) : 'N/A' }}
                                </span>

                                <h6 class="fw-bold small">{{ Str::limit($product->name, 22) }}</h6>
                                <p>{{ Str::limit($product->description, 40) }}</p>

                                @php
                                    $key = $product->supplier_id . '-' . $product->id;
                                    $discountRate = $discounts[$key]->discount_rate ?? 0;
                                    $priceAfterDiscount = $discountRate > 0 ? $product->price * (1 - $discountRate / 100) : $product->price;
                                @endphp

                                <div class="d-flex gap-2 align-items-center mb-2">
                                    @if($discountRate > 0)
                                        <small class="text-decoration-line-through text-muted">${{ $product->price }}</small>
                                        <small class="text-success fw-bold">${{ number_format($priceAfterDiscount,2) }}</small>
                                    @else
                                        <small class="fw-bold">${{ $product->price }}</small>
                                    @endif
                                </div>

                                @if($product->supplier)
                                    <a href="{{ route('supplier.products', $product->supplier->id) }}" class="small text-secondary text-decoration-none">
                                        <i class="bi bi-building me-1"></i>{{ Str::limit($product->supplier->name, 25) }}
                                    </a>
                                @endif
                            </div>
                        </a>

                        <div class="card-footer bg-white border-top p-2 d-flex justify-content-center">
                            <div class="cart-controls d-flex justify-content-center align-items-center gap-2"
                                 data-product-id="{{ $product->id }}" data-price="{{ $priceAfterDiscount }}">
                                <button class="btn btn-success btn-sm rounded-circle p-1 add-to-cart-btn">
                                    <i class="bi bi-cart-plus"></i>
                                </button>
                                <div class="quantity-controls d-none align-items-center gap-1">
                                    <button class="btn btn-outline-secondary btn-sm px-2 py-1 decrease">-</button>
                                    <span class="quantity small">1</span>
                                    <button class="btn btn-outline-secondary btn-sm px-2 py-1 increase">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- All Products --}}
    @if(isset($allProducts) && $allProducts->isNotEmpty())
        <h3 class="section-title section-title-secondary">
            <i class="bi bi-box-seam"></i> All Products
        </h3>
        <div class="row g-3">
            @foreach($allProducts as $product)
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="card h-100 shadow-sm product-card">
                        <a href="{{ route('products.showProduct', $product) }}" class="text-decoration-none text-dark">
                            <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/200x150?text=No+Image' }}"
                                class="card-img-top" alt="{{ $product->name }}" style="height: 150px; object-fit: cover;">

                            <div class="card-body">
                                <span class="badge rounded-pill" style="background-color: {{ $product->type ? '#20c997' : '#6c757d' }}; color: #fff;">
                                    {{ $product->type ? ucfirst($product->type) : 'N/A' }}
                                </span>

                                <h6 class="fw-bold small">{{ Str::limit($product->name, 22) }}</h6>
                                <p>{{ Str::limit($product->description, 40) }}</p>

                                @php
                                    $key = $product->supplier_id . '-' . $product->id;
                                    $discountRate = $discounts[$key]->discount_rate ?? 0;
                                    $priceAfterDiscount = $discountRate > 0 ? $product->price * (1 - $discountRate / 100) : $product->price;
                                @endphp

                                <div class="d-flex gap-2 align-items-center mb-2">
                                    @if($discountRate > 0)
                                        <small class="text-decoration-line-through text-muted">${{ $product->price }}</small>
                                        <small class="text-success fw-bold">${{ number_format($priceAfterDiscount,2) }}</small>
                                    @else
                                        <small class="fw-bold">${{ $product->price }}</small>
                                    @endif
                                </div>

                                @if($product->supplier)
                                    <a href="{{ route('supplier.products', $product->supplier->id) }}" class="small text-secondary text-decoration-none">
                                        <i class="bi bi-building me-1"></i>{{ Str::limit($product->supplier->name, 25) }}
                                    </a>
                                @endif
                            </div>
                        </a>

                        <div class="card-footer bg-white border-top p-2 d-flex justify-content-center">
                            <div class="cart-controls d-flex justify-content-center align-items-center gap-2"
                                 data-product-id="{{ $product->id }}" data-price="{{ $priceAfterDiscount }}">
                                <button class="btn btn-success btn-sm rounded-circle p-1 add-to-cart-btn">
                                    <i class="bi bi-cart-plus"></i>
                                </button>
                                <div class="quantity-controls d-none align-items-center gap-1">
                                    <button class="btn btn-outline-secondary btn-sm px-2 py-1 decrease">-</button>
                                    <span class="quantity small">1</span>
                                    <button class="btn btn-outline-secondary btn-sm px-2 py-1 increase">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @elseif(isset($products) && $products->isNotEmpty())
        <h3 class="section-title section-title-secondary">
            <i class="bi bi-building"></i> Products From
            <span class="ms-2 text-primary fw-semibold">{{ $products->first()->supplier->name ?? 'Unknown Supplier' }}</span>
        </h3>
        <div class="row g-3">
            @foreach($products as $product)
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="card h-100 shadow-sm product-card">
                        <a href="{{ route('products.showProduct', $product) }}" class="text-decoration-none text-dark">
                            <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/200x150?text=No+Image' }}"
                                class="card-img-top" alt="{{ $product->name }}" style="height: 150px; object-fit: cover;">

                            <div class="card-body">
                                <span class="badge rounded-pill" style="background-color: {{ $product->type ? '#20c997' : '#6c757d' }}; color: #fff;">
                                    {{ $product->type ? ucfirst($product->type) : 'N/A' }}
                                </span>

                                <h6 class="fw-bold small">{{ Str::limit($product->name, 22) }}</h6>
                                <p>{{ Str::limit($product->description, 40) }}</p>

                                @php
                                    $key = $product->supplier_id . '-' . $product->id;
                                    $discountRate = $discounts[$key]->discount_rate ?? 0;
                                    $priceAfterDiscount = $discountRate > 0 ? $product->price * (1 - $discountRate / 100) : $product->price;
                                @endphp

                                <div class="d-flex gap-2 align-items-center mb-2">
                                    @if($discountRate > 0)
                                        <small class="text-decoration-line-through text-muted">${{ $product->price }}</small>
                                        <small class="text-success fw-bold">${{ number_format($priceAfterDiscount,2) }}</small>
                                    @else
                                        <small class="fw-bold">${{ $product->price }}</small>
                                    @endif
                                </div>

                                @if($product->supplier)
                                    <a href="{{ route('supplier.products', $product->supplier->id) }}" class="small text-secondary text-decoration-none">
                                        <i class="bi bi-building me-1"></i>{{ Str::limit($product->supplier->name, 25) }}
                                    </a>
                                @endif
                            </div>
                        </a>

                        <div class="card-footer bg-white border-top p-2 d-flex justify-content-center">
                            <div class="cart-controls d-flex justify-content-center align-items-center gap-2"
                                 data-product-id="{{ $product->id }}" data-price="{{ $priceAfterDiscount }}">
                                <button class="btn btn-success btn-sm rounded-circle p-1 add-to-cart-btn">
                                    <i class="bi bi-cart-plus"></i>
                                </button>
                                <div class="quantity-controls d-none align-items-center gap-1">
                                    <button class="btn btn-outline-secondary btn-sm px-2 py-1 decrease">-</button>
                                    <span class="quantity small">1</span>
                                    <button class="btn btn-outline-secondary btn-sm px-2 py-1 increase">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-muted">No products available.</p>
    @endif

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
