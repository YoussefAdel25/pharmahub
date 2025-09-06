@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-2">Products by {{ $products->first()->supplier->name ?? 'N/A' }}</h2>



<div class="row">
    @foreach ($products as $product)
        @php
            $key = $product->supplier_id . '-' . $product->id;
            $discountRate = isset($discounts[$key]) ? $discounts[$key]->discount_rate : 0;
            $priceAfterDiscount = $discountRate > 0
                ? $product->price * (1 - $discountRate / 100)
                : $product->price;
        @endphp

        <div class="col-md-4 mb-4">
            <a href="{{ route('products.showProduct', $product) }}" class="text-decoration-none text-dark">
                <div class="card h-100 shadow-sm">
                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" style="height: 220px; object-fit: cover;">
                    @endif

                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($product->description, 60) }}</p>

                        <div class="mb-2">
                            <span class="fw-bold">Price: </span>
                            @if ($discountRate > 0)
                                <span class="text-decoration-line-through">${{ $product->price }}</span>
                                <span class="text-success fw-bold">${{ number_format($priceAfterDiscount, 2) }}</span>
                            @else
                                <span class="fw-bold">${{ $product->price }}</span>
                            @endif
                        </div>

                        <button class="btn btn-success w-100 add-to-cart-btn"
                                data-product-id="{{ $product->id }}"
                                data-price="{{ $priceAfterDiscount }}">
                            Add to Cart
                        </button>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>

</div>
@endsection
