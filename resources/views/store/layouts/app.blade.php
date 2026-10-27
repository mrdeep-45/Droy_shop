<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DPShop E-Commerce')</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Boxicons -->
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: #0d6efd !important;
            letter-spacing: -0.5px;
        }
        .nav-link {
            font-weight: 500;
            color: #555;
            transition: color 0.3s;
        }
        .nav-link:hover, .nav-link.active {
            color: #0d6efd;
        }
        .cart-badge {
            position: absolute;
            top: 0;
            right: 0;
            transform: translate(50%, -50%);
            font-size: 0.65rem;
        }
        .footer {
            background-color: #1a1d20;
            color: #adb5bd;
        }
        .footer a {
            color: #adb5bd;
            text-decoration: none;
            transition: color 0.3s;
        }
        .footer a:hover {
            color: #fff;
        }
        /* Product Card Styles */
        .product-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            background: #fff;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
        }
        .product-img-wrap {
            position: relative;
            overflow: hidden;
            aspect-ratio: 1/1;
            background: #f1f3f5;
        }
        .product-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        .product-card:hover .product-img-wrap img {
            transform: scale(1.05);
        }
        .badge-discount {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #dc3545;
            color: white;
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
        }
        .product-title {
            font-weight: 600;
            font-size: 1rem;
            color: #212529;
            text-decoration: none;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 8px;
        }
        .product-title:hover {
            color: #0d6efd;
        }
        .price-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .price-current {
            font-size: 1.15rem;
            font-weight: 700;
            color: #0d6efd;
        }
        .price-old {
            font-size: 0.9rem;
            color: #adb5bd;
            text-decoration: line-through;
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 80px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            color: #1a1d20;
            line-height: 1.2;
            margin-bottom: 20px;
        }
        .hero-subtitle {
            font-size: 1.25rem;
            color: #6c757d;
            margin-bottom: 30px;
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white py-3 sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('store.home') }}">
            <i class="bx bxs-shopping-bag text-primary"></i> DPShop
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-3">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('store.home') ? 'active' : '' }}" href="{{ route('store.home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('store.shop') ? 'active' : '' }}" href="{{ route('store.shop') }}">Shop</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Categories</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
            </ul>
            <div class="d-flex align-items-center gap-3">
                <form action="{{ route('store.shop') }}" method="GET" class="position-relative d-none d-lg-block">
                    <input type="text" name="search" class="form-control form-control-sm rounded-pill ps-3 pe-4" placeholder="Search products..." value="{{ request('search') }}">
                    <i class="bx bx-search position-absolute text-muted" style="right: 12px; top: 50%; transform: translateY(-50%);"></i>
                </form>
                
                <a href="{{ route('store.wishlist') }}" class="text-dark position-relative text-decoration-none">
                    <i class="bx bx-heart fs-4"></i>
                </a>
                
                <a href="{{ route('store.cart') }}" class="text-dark position-relative text-decoration-none me-3">
                    <i class="bx bx-cart fs-4"></i>
                    <span class="position-absolute badge rounded-pill bg-danger cart-badge" id="cartBadge">
                        {{ \App\Http\Controllers\ShopCartController::getCartCount() }}
                    </span>
                </a>

                @if(session()->has('customer_id'))
                    <div class="dropdown">
                        <a class="text-dark text-decoration-none dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight:600;">
                                {{ substr(session('customer_name', 'U'), 0, 1) }}
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                            <li><a class="dropdown-item" href="#"><i class="bx bx-user me-2"></i>My Account</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bx bx-package me-2"></i>My Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><i class="bx bx-log-out me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('store.login') }}" class="btn btn-outline-primary btn-sm px-4 rounded-pill fw-semibold">Login</a>
                @endif
            </div>
        </div>
    </div>
</nav>

<!-- Main Content -->
<main>
    @yield('content')
</main>

<!-- Footer -->
<footer class="footer pt-5 pb-4 mt-5">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-4 col-md-6">
                <h5 class="text-white fw-bold mb-3 d-flex align-items-center gap-2"><i class="bx bxs-shopping-bag text-primary"></i> DPShop</h5>
                <p class="mb-4 text-secondary">Your one-stop destination for premium quality garments and apparel. We deliver fashion that speaks.</p>
                <div class="d-flex gap-3">
                    <a href="#" class="fs-4"><i class="bx bxl-facebook-circle"></i></a>
                    <a href="#" class="fs-4"><i class="bx bxl-twitter"></i></a>
                    <a href="#" class="fs-4"><i class="bx bxl-instagram"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="text-white fw-semibold mb-3">Quick Links</h6>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                    <li><a href="{{ route('store.home') }}">Home</a></li>
                    <li><a href="{{ route('store.shop') }}">Shop All</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="text-white fw-semibold mb-3">Customer Care</h6>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                    <li><a href="#">My Account</a></li>
                    <li><a href="#">Track Order</a></li>
                    <li><a href="#">Return Policy</a></li>
                    <li><a href="#">FAQs</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-6">
                <h6 class="text-white fw-semibold mb-3">Newsletter</h6>
                <p class="text-secondary mb-3">Subscribe to get updates on new arrivals and special offers.</p>
                <form class="d-flex gap-2">
                    <input type="email" class="form-control" placeholder="Email address" style="background:#2a2e33; border:none; color:#fff;">
                    <button class="btn btn-primary px-3"><i class="bx bx-paper-plane"></i></button>
                </form>
            </div>
        </div>
        <hr class="my-4 border-secondary">
        <div class="row text-center text-md-start">
            <div class="col-md-6 text-secondary mb-2 mb-md-0">
                &copy; {{ date('Y') }} DPShop. All rights reserved.
            </div>
            <div class="col-md-6 text-md-end">
                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" alt="PayPal" height="20" class="me-2 opacity-50">
                <img src="https://upload.wikimedia.org/wikipedia/commons/a/a4/Mastercard_2019_logo.svg" alt="Mastercard" height="20" class="me-2 opacity-50">
                <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" alt="Visa" height="20" class="opacity-50">
            </div>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stack('scripts')
</body>
</html>
