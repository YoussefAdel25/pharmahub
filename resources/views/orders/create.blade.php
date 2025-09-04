@extends('layouts.app')

@section('content')
<h1>Create Order</h1>
<form method="POST" action="{{ route('orders.store') }}">
    @csrf
    <div class="mb-3">
        <label>Product</label>
        <select name="items[0][product_id]" class="form-control">
            @foreach ($products as $product)
                <option value="{{ $product->id }}">{{ $product->name }} - ${{ $product->price }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label>Quantity</label>
        <input name="items[0][quantity]" type="number" min="1" class="form-control">
    </div>
    <button class="btn btn-success">Place Order</button>
</form>
@endsection
