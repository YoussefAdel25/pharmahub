@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Products by {{ $supplier->name }}</h2>
    <div class="row">
        @foreach ($products as $product)
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/200x200.png?text=No+Image' }}"
                     class="card-img-top" style="height:200px; object-fit:cover;" alt="{{ $product->name }}">
                <div class="card-body">
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p class="card-text text-muted">{{ $product->description }}</p>
                    <p class="fw-bold">
                        Price: ${{ $product->price }}
                        @if($product->wholesale_price)
                            <br>Wholesale: ${{ $product->wholesale_price }}
                        @endif
                    </p>
                    @php
                        $discount = $discounts[$product->supplier_id.'-'.$product->id]->discount_rate ?? 0;
                    @endphp
                    @if($discount)
                        <p class="text-success">Discount: {{ $discount }}%</p>
                    @endif
                    @if($product->quota_limit)
                        <p class="text-warning small">
                            Max per {{ $product->quota_period ?? 'order' }}: {{ $product->quota_limit }}
                        </p>
                    @endif
                </div>
                <div class="card-footer text-center">
                    <div class="cart-controls"
                         data-product-id="{{ $product->id }}"
                         data-price="{{ $product->price }}"
                         data-quota="{{ $product->quota_limit ?? 0 }}">
                        <button type="button" class="btn btn-success w-100 add-to-cart-btn">Add to Cart</button>
                        <div class="quantity-controls d-none mt-2">
                            <div class="d-flex justify-content-center align-items-center">
                                <button type="button" class="btn btn-sm btn-secondary decrease">-</button>
                                <span class="mx-2 quantity">1</span>
                                <button type="button" class="btn btn-sm btn-secondary increase">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div id="cart-toast" style="position: fixed; top:20px; right:20px; z-index:1050; display:none;">
        <div class="alert alert-success mb-0">Product added to the basket successfully!</div>
    </div>
</div>
@endsection

@section('js')
<script>
$(function() {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    function showToast(message) {
        $('#cart-toast .alert').text(message).fadeIn();
        setTimeout(() => { $('#cart-toast').fadeOut(); }, 2000);
    }

    function updateQuantity(container, qty) {
        let quota = parseInt(container.data('quota')) || 0;
        if(quota && qty > quota){
            alert("You cannot add more than allowed quota!");
            qty = quota;
        }
        container.find('.quantity').text(qty);
        let price = parseFloat(container.data('price'));
        container.find('span.total-price').text('$' + (qty * price).toFixed(2));

        $.post('/cart/update', { product_id: container.data('product-id'), quantity: qty });
        $.get('/cart/count', function(count){ $('#cart-count').text(count); });
    }

    $(document).on('click', '.add-to-cart-btn', function(){
        let container = $(this).closest('.cart-controls');
        $.post('/cart/add', { product_id: container.data('product-id'), quantity: 1 })
        .done(function(response){
            if(response.success){
                container.find('.add-to-cart-btn').addClass('d-none');
                container.find('.quantity-controls').removeClass('d-none');
                updateQuantity(container, response.quantity);
                showToast('Product added to the basket successfully!');
            }
        });
    });

    $(document).on('click', '.increase', function(){
        let container = $(this).closest('.cart-controls');
        let qty = parseInt(container.find('.quantity').text()) + 1;
        updateQuantity(container, qty);
    });

    $(document).on('click', '.decrease', function(){
        let container = $(this).closest('.cart-controls');
        let qty = parseInt(container.find('.quantity').text()) - 1;
        if(qty < 1) return;
        updateQuantity(container, qty);
    });

    $.get('/cart/count', function(count){ $('#cart-count').text(count); });
});
</script>
@endsection
