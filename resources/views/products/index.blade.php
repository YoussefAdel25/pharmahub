@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Products</h1>
        {{--  @can('isSupplier')  --}}
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                + Add Product
            </a>
        {{--  @endcan  --}}
    </div>

    <div class="row">
        @foreach ($products as $product)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">

                    {{-- Product Image --}}
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}"
                             class="card-img-top"
                             alt="{{ $product->name }}"
                             style="height: 200px; object-fit: cover;">
                    @else
                        <img src="{{ asset('images/default.png') }}"
                             class="card-img-top"
                             alt="No Image"
                             style="height: 200px; object-fit: cover;">
                    @endif

                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text text-muted">{{ $product->description }}</p>
                        <p class="fw-bold mb-1">Price: ${{ $product->price }}</p>
                        <p class="text-secondary">Supplier: {{ $product->supplier->name }}</p>
                    </div>

                    <div class="card-footer d-flex justify-content-between">
                        @can('isSupplier')
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        @endcan
                        @can('isPharmacy')
                            <a href="{{ route('orders.create', ['product' => $product->id]) }}" class="btn btn-success btn-sm">Order</a>
                        @endcan
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
