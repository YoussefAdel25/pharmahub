@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Add Product</h1>

    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label>Name</label>
            <input name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label>Price</label>
            <input name="price" type="number" step="0.01" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Stock</label>
            <input name="stock" type="number" class="form-control" value="0" required>
        </div>

        <div class="mb-3">
            <label>Quota Limit</label>
            <input name="quota_limit" type="number" class="form-control">
        </div>



        <div class="mb-3">
            <label>Product Image</label>
            <input type="file" name="image" class="form-control">
        </div>

        <button class="btn btn-primary">Save</button>
    </form>
</div>
@endsection
