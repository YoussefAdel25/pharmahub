<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaHub</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f8f9fa;
        }

        .nav-link {
            color: #fff !important;
        }

        .nav-link:hover {
            background: #495057;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid">
            <!-- Logo -->
            <a class="navbar-brand fw-bold" href="/">PharmaHub</a>

            <!-- Toggle for mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu Items -->
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                    {{-- ✅ Admin --}}
                    @if (Auth::user() && Auth::user()->role === 'admin')
                        <li class="nav-item"><a href="{{ route('allProducts.index') }}" class="nav-link">Manage
                                Products</a></li>
                        <li class="nav-item"><a href="{{ route('allOrders.index') }}" class="nav-link">Manage Orders</a>
                        </li>
                        <li class="nav-item"><a href="{{ route('users.index') }}" class="nav-link">Manage Users</a></li>
                        <li class="nav-item"><a href="{{ route('region.index') }}" class="nav-link">Manage Regions</a>
                        </li>
                    @endif

                    {{-- ✅ Supplier --}}
                    @if (Auth::user() && Auth::user()->role === 'supplier')
                        <li class="nav-item"><a href="{{ route('products.index') }}" class="nav-link">My Products</a>
                        </li>
                        <li class="nav-item"><a href="{{ route('supplier.orders.index') }}" class="nav-link">Orders</a>
                        </li>
                        <li class="nav-item"><a href="{{ route('regions.suppliers') }}" class="nav-link">Delivery
                                Regions</a></li>
                    @endif

                    {{-- ✅ Customer --}}
                    @if (Auth::user() && Auth::user()->role === 'customer')
                        {{--  <li class="nav-item"><a href="{{ route('products.productsForCustomer') }}"
                                class="nav-link">Browse Products</a></li>  --}}
                        <li class="nav-item"><a href="{{ route('orders.index') }}" class="nav-link">My Orders</a></li>
                    @endif
                </ul>

                <ul class="navbar-nav ms-auto">
                    @auth
                        @if (auth()->user()->role === 'customer')
                            <li class="nav-item me-3">
                                <a class="nav-link position-relative" data-bs-toggle="modal" data-bs-target="#cartModal">
                                    <i class="bi bi-cart"></i>
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                        id="cart-count">0</span>
                                </a>
                            </li>
                        @endif

                        <!-- ✅ Dropdown for User Settings -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i
                                            class="bi bi-gear me-2"></i>Personal Settings</a></li>

                                <li><a class="dropdown-item" href="{{ route('orders.index') }}"><i
                                            class="bi bi-bag-check me-2"></i>My Orders</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button class="dropdown-item text-danger"><i
                                                class="bi bi-box-arrow-right me-2"></i>Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        {{--  <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>  --}}
                    @endauth
                </ul>

            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="container mt-4">
        @yield('content')
    </div>

    <!-- Cart Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Your Cart</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="cart-items"></div>
                <div class="modal-footer">
                    <strong>Total: $<span id="cart-total">0</span></strong>
                    <form action="{{ route('cart.checkout') }}" method="POST">
                        @csrf
                        <button type="submit" id="checkout-btn" class="btn btn-primary">Checkout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('js')
</body>

</html>
