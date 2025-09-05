<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>PharmaHub</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { display: flex; }
.sidebar { width: 250px; height: 100vh; position: fixed; top:0; left:0; background:#212529; color:#fff; }
.sidebar a { display:block; padding:10px 15px; color:#fff; text-decoration:none; }
.sidebar a:hover { background:#495057; }
.main-content { margin-left:250px; width: calc(100% - 250px); min-height:100vh; background:#f8f9fa; }
</style>
</head>
<body>
<x-sidebar />
<div class="main-content">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">PharmaHub</a>
            <ul class="navbar-nav ms-auto">
                @auth
                    @if(auth()->user()->role === 'pharmacy')
                        <li class="nav-item me-3">
                            <a class="nav-link position-relative" id="cart-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16">
                                    <path d="M0 1.5A.5.5 0 0 1 .5 1h1a.5.5 0 0 1 .485.379L2.89 5H14.5a.5.5 0 0 1 .49.598l-1.5 7A.5.5 0 0 1 13 13H4a.5.5 0 0 1-.491-.408L1.01 1.607 0 1.5zm3.14 4l1.25 5.5H13l1.25-5.5H3.14zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 2a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/>
                                </svg>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cart-count">0</span>
                            </a>
                        </li>
                    @endif
                    <li class="nav-item"><span class="nav-link">{{ auth()->user()->name }}</span></li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-link nav-link">Logout</button></form>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                @endauth
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        @yield('content')
    </div>
</div>

<!-- Cart Modal -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Your Cart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="cart-items">
                {{-- AJAX will load cart items here --}}
            </div>
            <div class="modal-footer">
                <strong>Total: $<span id="cart-total">0</span></strong>
                <button type="button" class="btn btn-primary">Checkout</button>
            </div>
        </div>
    </div>
</div>

<div id="cart-toast" style="position: fixed; top:20px; right:20px; z-index:1050; display:none;">
    <div class="alert alert-success mb-0">Product added to the basket successfully!</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@yield('js')
</body>
</html>
