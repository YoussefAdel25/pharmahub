<h2>Suppliers in your region</h2>
<ul>
@foreach($suppliers as $supplier)
    <li>
        <a href="{{ route('supplier.products', $supplier->id) }}">
            {{ $supplier->name }}
        </a>
    </li>
@endforeach
</ul>
