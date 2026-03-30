<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')::Flexicell Mobile</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/logo.png') }}" />
    <!-- Google font -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700" rel="stylesheet">
    <!-- Bootstrap -->
    <link type="text/css" rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
    <!-- Slick -->
    <link type="text/css" rel="stylesheet" href="{{ asset('css/slick.css') }}" />
    <link type="text/css" rel="stylesheet" href="{{ asset('css/slick-theme.css') }}" />
    <!-- nouislider -->
    <link type="text/css" rel="stylesheet" href="{{ asset('css/nouislider.min.css') }}" />
    <!-- Font Awesome Icon -->
    <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}">
    <!-- Custom stlylesheet -->
    <link type="text/css" rel="stylesheet" href="{{ asset('css/style.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @yield('style')
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f8f9fa;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23d10024' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        /* Custom Dropdown Styling for Electro Theme */
        #navigation .main-nav .dropdown:hover>.dropdown-menu {
            display: block;
            opacity: 1;
            visibility: visible;
            margin-top: 0;
            transition: 0.3s all;
        }

        #navigation .main-nav .dropdown-menu {
            display: none;
            position: absolute;
            background-color: #FFF;
            min-width: 200px;
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.1);
            border: none;
            border-top: 3px solid #D10024;
            border-radius: 0;
            z-index: 1000;
            padding: 10px 0;
        }

        #navigation .main-nav .dropdown-menu li {
            display: block;
            margin-left: 0 !important;
        }

        #navigation .main-nav .dropdown-menu li a {
            padding: 10px 20px !important;
            font-weight: 500;
            color: #2B2D42 !important;
            display: block;
            transition: 0.2s all;
            border-bottom: 1px solid #f1f1f1;
            line-height: normal !important;
            height: auto !important;
            background: none !important;
        }

        #navigation .main-nav .dropdown-menu li:last-child a {
            border-bottom: none;
        }

        #navigation .main-nav .dropdown-menu li a:hover {
            color: #D10024 !important;
            background-color: #FBFBFC !important;
            padding-left: 25px !important;
        }

        /* Red underline animation for dropdown toggle */
        #navigation .main-nav .dropdown>a:after {
            content: "";
            display: block;
            width: 0%;
            height: 2px;
            background-color: #D10024;
            transition: 0.2s all;
        }

        #navigation .main-nav .dropdown:hover>a:after,
        #navigation .main-nav .dropdown.active>a:after {
            width: 100%;
        }

        /* Adjustments for the dropdown toggle link */
        .main-nav>li.dropdown>a {
            padding: 20px 0px;
        }
    </style>
</head>

<body>
    <!-- HEADER -->
    <header>
        <!-- TOP HEADER -->
        <div id="top-header">
            <div class="container">
                <ul class="header-links pull-left">
                    <li><a href="#"><i class="fa fa-phone"></i> 09978844466</a></li>
                    <li><a href="#"><i class="fa fa-envelope-o"></i> flexicell.main.mdy@gmail.com</a></li>
                    <li><a href="#"><i class="fa fa-map-marker"></i> MANDALAY</a></li>
                </ul>
                <ul class="header-links pull-right">
                    @if (Auth::check() && Auth::user()->role == 'user')
                        <li><a href="#"><i class="fa fa-user-o"></i> My Account</a></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" id="logout">
                                @csrf
                                <a class="logout" style="cursor: pointer;"><i class="fa fa-lock"></i> Logout</a>
                            </form>
                        </li>
                    @else
                        <li><a href="{{ route('login') }}"><i class="fa fa-lock"></i>Login</a></li>
                    @endif
                </ul>
            </div>
        </div>
        <!-- /TOP HEADER -->

        <!-- MAIN HEADER -->
        <div id="header">
            <!-- container -->
            <div class="container">
                <!-- row -->
                <div class="row">
                    <!-- LOGO -->
                    <div class="col-md-3">
                        <div class="header-logo">
                            <a href="{{ route('home') }}" class="logo">
                                {{-- <img src="{{ asset('img/logoremove.png') }}" alt=""> --}}
                            </a>
                        </div>
                    </div>
                    <!-- /LOGO -->

                    <!-- SEARCH BAR -->
                    <div class="col-md-6">
                        <div class="header-search">
                            <form action="{{ route('products.search') }}" method="GET">
                                <select class="input-select" name="category_id">
                                    <option value="">All Categories</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(request()->input('category_id') == $category->id)>
                                            {{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <input class="input" placeholder="Search here" type="text" name="query"
                                    value="{{ request()->input('query') }}">
                                <button class="search-btn">Search</button>
                            </form>
                        </div>
                    </div>
                    <!-- /SEARCH BAR -->

                    <!-- ACCOUNT -->
                    <div class="col-md-3 clearfix">
                        <div class="header-ctn">
                            <!-- Cart -->
                            {{-- <div class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-shopping-cart"></i>
                                    <span>Your Cart</span>
                                    <div class="qty">3</div>
                                </a>
                                <div class="cart-dropdown">
                                    <div class="cart-list">
                                        <div class="product-widget">
                                            <div class="product-img">
                                                <img src="./img/product01.png" alt="">
                                            </div>
                                            <div class="product-body">
                                                <h3 class="product-name"><a href="#">product name goes here</a>
                                                </h3>
                                                <h4 class="product-price"><span class="qty">1x</span>$980.00</h4>
                                            </div>
                                            <button class="delete"><i class="fa fa-close"></i></button>
                                        </div>

                                        <div class="product-widget">
                                            <div class="product-img">
                                                <img src="./img/product02.png" alt="">
                                            </div>
                                            <div class="product-body">
                                                <h3 class="product-name"><a href="#">product name goes here</a>
                                                </h3>
                                                <h4 class="product-price"><span class="qty">3x</span>$980.00</h4>
                                            </div>
                                            <button class="delete"><i class="fa fa-close"></i></button>
                                        </div>
                                    </div>
                                    <div class="cart-summary">
                                        <small>3 Item(s) selected</small>
                                        <h5>SUBTOTAL: $2940.00</h5>
                                    </div>
                                    <div class="cart-btns">
                                        <a href="#">View Cart</a>
                                        <a href="#">Checkout <i class="fa fa-arrow-circle-right"></i></a>
                                    </div>
                                </div>
                            </div> --}}
                            <!-- /Cart -->

                            <!-- Menu Toogle -->
                            <div class="menu-toggle">
                                <a href="#">
                                    <i class="fa fa-bars"></i>
                                    <span>Menu</span>
                                </a>
                            </div>
                            <!-- /Menu Toogle -->
                        </div>
                    </div>
                    <!-- /ACCOUNT -->
                </div>
                <!-- row -->
            </div>
            <!-- container -->
        </div>
        <!-- /MAIN HEADER -->
    </header>
    <!-- /HEADER -->

    <!-- NAVIGATION -->
    <nav id="navigation">
        <!-- container -->
        <div class="container">
            <!-- responsive-nav -->
            <div id="responsive-nav">
                <!-- NAV -->
                <ul class="main-nav nav navbar-nav">
                    <li class="{{ request()->routeIs('home') ? 'active' : '' }}"><a
                            href="{{ route('home') }}">Home</a></li>
                    <li class="dropdown {{ request()->has('category_id') || request()->routeIs('products') ? 'active' : '' }}">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="{{ route('products') }}"
                            aria-expanded="true" style="cursor: pointer;">
                            Products <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu">
                            @foreach ($categories as $cat)
                                <li class="{{ request()->input('category_id') == $cat->id ? 'active' : '' }}">
                                    <a
                                        href="{{ route('products.search', ['category_id' => $cat->id]) }}">{{ $cat->category_name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                            <li class="{{ request()->routeIs('trade_in') ? 'active' : '' }}"><a
                                    href="{{ route('trade_in') }}"> Trade-In</a></li>
                                    <li class="{{ request()->routeIs('warranty.check') ? 'active' : '' }}"><a
                                            href="{{ route('warranty.check') }}">Warranty check</a></li>
                            <li class="{{ request()->routeIs('blogs.index') || request()->routeIs('blogs.show') ? 'active' : '' }}"><a
                                    href="{{ route('blogs.index') }}">Blog</a></li>
                            <li class="{{ request()->routeIs('about') ? 'active' : '' }}"><a
                                    href="{{ route('about') }}">About</a></li>
                            <li class="{{ request()->routeIs('contact') ? 'active' : '' }}"><a
                                    href="{{ route('contact') }}">Contact</a></li>
                </ul>
                <!-- /NAV -->
            </div>
            <!-- /responsive-nav -->
        </div>
        <!-- /container -->
    </nav>
    <!-- /NAVIGATION -->

    @yield('content')

    <!-- FOOTER -->
    <footer id="footer">
        <!-- top footer -->
        <div class="section">
            <!-- container -->
            <div class="container">
                <!-- row -->
                <div class="row">
                    <div class="col-md-3 col-xs-6">
                        <div class="footer">
                            <h3 class="footer-title">About Us</h3>
                            <p></p>
                            <ul class="footer-links">
                                <li><a href="#"><i class="fa fa-map-marker"></i>MANDALAY Region</a></li>
                                <li><a href="#"><i class="fa fa-phone"></i>09978844466</a></li>
                                <li><a href="#"><i class="fa fa-envelope-o"></i>flexicell.main.mdy@gmail.com</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-3 col-xs-6">
                        <div class="footer">
                            <h3 class="footer-title">Categories</h3>
                            <ul class="footer-links">
                                @foreach ($categories as $cat)
                                    <li><a
                                            href="{{ route('products.search', ['category_id' => $cat->id]) }}">{{ $cat->category_name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="clearfix visible-xs"></div>

                    <div class="col-md-3 col-xs-6">
                        <div class="footer">
                            <h3 class="footer-title">Information</h3>
                            <ul class="footer-links">
                                <li><a href="{{ route('about') }}">About Us</a></li>
                                <li><a href="{{ route('contact') }}">Contact Us</a></li>
                                <li><a href="{{ route('blogs.index') }}">Blog</a></li>
                                <li><a href="#">Privacy Policy</a></li>
                                <li><a href="#">Orders and Returns</a></li>
                                <li><a href="#">Terms & Conditions</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-3 col-xs-6">
                        <div class="footer">
                            <h3 class="footer-title">Service</h3>
                            <ul class="footer-links">
                                <li><a href="#">My Account</a></li>
                                <li><a href="#">View Cart</a></li>
                                <li><a href="#">Wishlist</a></li>
                                <li><a href="#">Track My Order</a></li>
                                <li><a href="#">Help</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /row -->
            </div>
            <!-- /container -->
        </div>
        <!-- /top footer -->

        <!-- bottom footer -->
        <div id="bottom-footer" class="section">
            <div class="container">
                <!-- row -->
                <div class="row">
                    <div class="col-md-12 text-center">
                        <span class="copyright">
                            <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                            Copyright &copy;
                            <script>
                                document.write(new Date().getFullYear());
                            </script> All rights reserved | FLEXICELL MOBILE <i
                                class="fa fa-heart-o" aria-hidden="true"></i> by <a href="#"
                                target="_blank">HTET MYAT THU</a>
                            <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                        </span>
                    </div>
                </div>
                <!-- /row -->
            </div>
            <!-- /container -->
        </div>
        <!-- /bottom footer -->
    </footer>
    <!-- /FOOTER -->

    <!-- jQuery Plugins -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/slick.min.js') }}"></script>
    <script src="{{ asset('js/nouislider.min.js') }}"></script>
    <script src="{{ asset('js/jquery.zoom.min.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    @yield('script')
    <script>
        $(document).ready(function() {
            $('.logout').click(function(e) {
                // e.preventDefalut()
                $('#logout').submit()
            })
        })
    </script>
</body>

</html>
