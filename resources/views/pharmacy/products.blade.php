@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Products by {{ $supplier->name }}</h2>
    <div class="row">
        @foreach ($products as $product)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/200x200.png?text=No+Image' }}" class="card-img-top" style="height:200px; object-fit:cover;" alt="{{ $product->name }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text text-muted">{{ $product->description }}</p>
                        <p class="fw-bold">Price: ${{ $product->price }}</p>
                    </div>
                    <div class="card-footer text-center">
                        <div class="cart-controls" data-product-id="{{ $product->id }}">
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
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var cartModal = new bootstrap.Modal(document.getElementById('cartModal'));

    function showToast(message) {
        $('#cart-toast .alert').text(message).fadeIn();
        setTimeout(() => { $('#cart-toast').fadeOut(); }, 2000);
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

        $.post('/cart/add', { product_id: productId, quantity: 1 }, function(response) {
            if(response.success) {
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
        let qtySpan = $(this).siblings('.quantity');
        let qty = parseInt(qtySpan.text()) + 1;
        qtySpan.text(qty);
        let container = $(this).closest('.cart-controls');

        $.post('/cart/update', { product_id: container.data('product-id'), quantity: qty }, function(response) {
            if(response.success) loadCart();
        });
    });

    $(document).on('click', '.decrease', function() {
        let qtySpan = $(this).siblings('.quantity');
        let qty = parseInt(qtySpan.text()) - 1;
        if(qty < 1) return;
        qtySpan.text(qty);
        let container = $(this).closest('.cart-controls');

        $.post('/cart/update', { product_id: container.data('product-id'), quantity: qty }, function(response) {
            if(response.success) loadCart();
        });
    });
});
</script>
@endsection
