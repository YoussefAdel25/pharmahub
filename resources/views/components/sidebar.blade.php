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
            <li><a href="{{ route('orders.index') }}">Orders from Pharmacies</a></li>
            <li><a href="{{ route('regions.suppliers') }}">Delivery Regions</a></li>

        @endif

        {{-- ✅ Customer --}}
        @if(Auth::user() && Auth::user()->role === 'Customer')
            <li><a href="{{ route('suppliers.index') }}">Browse Products</a></li>
            <li><a href="">My Orders</a></li>
        @endif
    </ul>
</div>
