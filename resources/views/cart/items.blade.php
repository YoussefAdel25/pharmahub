@foreach($cartItems as $item)
<div class="d-flex justify-content-between align-items-center mb-2 cart-controls" data-product-id="{{ $item->product->id }}">
    <span>{{ $item->product->name }} (x<span class="quantity">{{ $item->quantity }}</span>)</span>
    <div>
        <button class="btn btn-sm btn-secondary decrease">-</button>
        <button class="btn btn-sm btn-secondary increase">+</button>
        <span>${{ $item->product->price * $item->quantity }}</span>
    </div>
</div>
@endforeach
