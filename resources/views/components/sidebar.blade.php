<div class="sidebar">
    <div class="p-3 fs-4 fw-bold border-bottom">PharmaHub</div>
    <ul class="list-unstyled mt-3">
        <li>
            <a href="{{ url('/') }}">Dashboard</a>
        </li>

        {{-- ✅ Admin --}}
        @if(Auth::user() && Auth::user()->role === 'admin')
            <li><a href="{{ route('products.index') }}">Manage Products</a></li>
            <li><a href="{{ route('orders.index') }}">Manage Orders</a></li>
            <li><a href="{{ route('users.index') }}">Manage Users</a></li>
            <li><a href="{{ route('region.index') }}">Manage Regions</a></li>
        @endif

        {{-- ✅ Supplier --}}
        @if(Auth::user() && Auth::user()->role === 'supplier')
            <li><a href="{{ route('products.index') }}">My Products</a></li>
            <li><a href="{{ route('supplier.orders.index') }}">Orders from Customers</a></li>
            <li><a href="{{ route('regions.suppliers') }}">Delivery Regions</a></li>

        @endif

        {{-- ✅ Customer --}}
        @if(Auth::user() && Auth::user()->role === 'customer')
            <li><a href="{{ route('products.productsForCustomer') }}">Browse Products</a></li>
            <li><a href="{{ route('orders.index') }}">My Orders</a></li>
        @endif
    </ul>
</div>
