@extends('layouts.user')
@section('title', 'Home')
@section('style')
    <style>
        .product-img {
            position: relative;
        }

        .brand {
            position: absolute;
            max-width: 30px;
            height: 30px;
            top: 10px;
            right: 10px;
            border-radius: 10px;
        }
    </style>
@endsection
@section('content')
    <!-- SECTION -->
    <div class="section">
        <!-- container -->
        <div class="container">
            <!-- row -->
            <div class="row">
                @foreach ($categories->take(3) as $category)
                    <!-- shop -->
                    <div class="col-md-4 col-xs-6">
                        <div class="shop">
                            <div class="shop-img">
                                <img src="{{ asset('img/logo.png') }}" alt="">
                            </div>
                            <div class="shop-body">
                                <h3>{{ $category->category_name }}<br>Collection</h3>
                                <a href="{{ route('products.search', ['category_id' => $category->id]) }}"
                                    class="cta-btn">Look Now <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <!-- /shop -->
                @endforeach
            </div>
            <!-- /row -->
        </div>
        <!-- /container -->
    </div>
    <!-- /SECTION -->

    <!-- SECTION -->
    <div class="section">
        <!-- container -->
        <div class="container">
            <!-- row -->
            <div class="row">

                <!-- section title -->
                <div class="col-md-12">
                    <div class="section-title">
                        <h3 class="title">New Arrivals</h3>
                        <div class="section-nav">
                            <ul class="section-tab-nav tab-nav">
                                @foreach ($categories as $key => $category)
                                    <li class="{{ $key == 0 ? 'active' : '' }}"><a data-toggle="tab"
                                            href="#tab1">{{ $category->name }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /section title -->

                <!-- Products tab & slick -->
                <div class="col-md-12">
                    <div class="row">
                        <div class="products-tabs">
                            <!-- tab -->
                            <div id="tab1" class="tab-pane active">
                                <div class="products-slick" data-nav="#slick-nav-1">
                                    @foreach ($new_products as $product)
                                        <!-- product -->

                                        <div class="product">
                                            <div class="product-img">
                                                @php
                                                    $phoneModel = $product->phoneModel;
                                                    $brand = $phoneModel?->brand;
                                                    $images = is_array($product->image) ? $product->image : (array) json_decode($product->image, true);
                                                    $firstImage = $images[0] ?? null;
                                                @endphp
                                                @if ($brand && $brand->logo)
                                                    <img src="{{ $brand->logoUrl() }}"
                                                        alt="{{ $brand->brand_name ?? '' }}" class="brand">
                                                @endif
                                                @if ($firstImage)
                                                    <img src="{{ asset('storage/products/' . $firstImage) }}" alt="">
                                                @endif
                                                @if ($product->warranty_month)
                                                    <div class="product-label">
                                                        <span class="sale">{{ $product->warranty_month }}M Warranty</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="product-body">
                                                <p class="product-category">{{ $phoneModel?->category?->category_name }}</p>
                                                <h3 class="product-name">
                                                    <a href="{{ route('products.show', $product->id) }}">
                                                        {{ $phoneModel?->model_name }}
                                                    </a>
                                                </h3>
                                                <h4 class="product-price">
                                                    {{ number_format($product->selling_price) }} MMK
                                                </h4>
                                                <div class="product-rating">
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                </div>
                                                <div class="product-btns">
                                                    <button class="quick-view"><a
                                                            href="{{ route('products.show', $product->id) }}"><i
                                                                class="fa fa-eye"></i><span class="tooltipp">quick
                                                                view</span></a></button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /product -->
                                    @endforeach
                                </div>
                                <div id="slick-nav-1" class="products-slick-nav"></div>
                            </div>
                            <!-- /tab -->
                        </div>
                    </div>
                </div>
                <!-- Products tab & slick -->
            </div>
            <!-- /row -->
        </div>
        <!-- /container -->
    </div>
    <!-- /SECTION -->

    <!-- HOT DEAL SECTION -->
    <div id="hot-deal" class="section">
        <!-- container -->
        <div class="container">
            <!-- row -->
            <div class="row">
                <div class="col-md-12">
                    <div class="hot-deal">
                        <ul class="hot-deal-countdown">
                            <li>
                                <div>
                                    <h3>02</h3>
                                    <span>Days</span>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <h3>10</h3>
                                    <span>Hours</span>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <h3>34</h3>
                                    <span>Mins</span>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <h3>60</h3>
                                    <span>Secs</span>
                                </div>
                            </li>
                        </ul>
                        <h2 class="text-uppercase">hot deal this week</h2>
                        <p>New Collection Up to 50% OFF</p>
                        <a class="primary-btn cta-btn" href="#">Shop now</a>
                    </div>
                </div>
            </div>
            <!-- /row -->
        </div>
        <!-- /container -->
    </div>
    <!-- /HOT DEAL SECTION -->

    <!-- POPULAR PRODUCTS SECTION -->
    <div class="section">
        <!-- container -->
        <div class="container">
            <!-- row -->
            <div class="row">

                <!-- section title -->
                <div class="col-md-12">
                    <div class="section-title">
                        <h3 class="title">Popular Products</h3>
                        <div class="section-nav">
                            <div id="slick-nav-2" class="products-slick-nav"></div>
                        </div>
                    </div>
                </div>
                <!-- /section title -->

                <!-- Products tab & slick -->
                <div class="col-md-12">
                    <div class="row">
                        <div class="products-tabs">
                            <!-- tab -->
                            <div id="tab2" class="tab-pane fade in active">
                                <div class="products-slick" data-nav="#slick-nav-2">
                                    @foreach ($popular_products as $product)
                                        <!-- product -->
                                        <div class="product">
                                            <div class="product-img">
                                                @php
                                                    $phoneModel = $product->phoneModel;
                                                    $brand = $phoneModel?->brand;
                                                    $images = is_array($product->image) ? $product->image : (array) json_decode($product->image, true);
                                                    $firstImage = $images[0] ?? null;
                                                @endphp
                                                @if ($brand && $brand->logo)
                                                    <img src="{{ $brand->logoUrl() }}"
                                                        alt="{{ $brand->brand_name ?? '' }}" class="brand">
                                                @endif
                                                @if ($firstImage)
                                                    <img src="{{ asset('storage/products/' . $firstImage) }}" alt="">
                                                @endif
                                                @if ($product->warranty_month)
                                                    <div class="product-label">
                                                        <span class="sale">{{ $product->warranty_month }}M Warranty</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="product-body">
                                                <p class="product-category">{{ $phoneModel?->category?->category_name }}</p>
                                                <h3 class="product-name">
                                                    <a href="{{ route('products.show', $product->id) }}">
                                                        {{ $phoneModel?->model_name }}
                                                    </a>
                                                </h3>
                                                <h4 class="product-price">
                                                    {{ number_format($product->selling_price) }} MMK
                                                </h4>
                                                <div class="product-rating">
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                </div>
                                                <div class="product-btns">
                                                    <button class="quick-view"><a
                                                            href="{{ route('products.show', $product->id) }}"><i
                                                                class="fa fa-eye"></i><span class="tooltipp">quick
                                                                view</span></a></button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /product -->
                                    @endforeach
                                </div>
                                <div id="slick-nav-2" class="products-slick-nav"></div>
                            </div>
                            <!-- /tab -->
                        </div>
                    </div>
                </div>
                <!-- /Products tab & slick -->
            </div>
            <!-- /row -->
        </div>
        <!-- /container -->
    </div>
    <!-- /POPULAR PRODUCTS SECTION -->

    <!-- BEST SELLERS SECTION -->
    <div class="section">
        <!-- container -->
        <div class="container">
            <!-- row -->
            <div class="row">

                <!-- section title -->
                <div class="col-md-12">
                    <div class="section-title">
                        <h3 class="title">Best Sellers</h3>
                        <div class="section-nav">
                            <div id="slick-nav-3" class="products-slick-nav"></div>
                        </div>
                    </div>
                </div>
                <!-- /section title -->

                <!-- Products tab & slick -->
                <div class="col-md-12">
                    <div class="row">
                        <div class="products-tabs">
                            <!-- tab -->
                            <div id="tab3" class="tab-pane fade in active">
                                <div class="products-slick" data-nav="#slick-nav-3">
                                    @foreach ($best_sellers as $product)
                                        <!-- product -->
                                        <div class="product">
                                            <div class="product-img">
                                                @php
                                                    $phoneModel = $product->phoneModel;
                                                    $brand = $phoneModel?->brand;
                                                    $images = is_array($product->image) ? $product->image : (array) json_decode($product->image, true);
                                                    $firstImage = $images[0] ?? null;
                                                @endphp
                                                @if ($brand && $brand->logo)
                                                    <img src="{{ $brand->logoUrl() }}"
                                                        alt="{{ $brand->brand_name ?? '' }}" class="brand">
                                                @endif
                                                @if ($firstImage)
                                                    <img src="{{ asset('storage/products/' . $firstImage) }}" alt="">
                                                @endif
                                                @if ($product->warranty_month)
                                                    <div class="product-label">
                                                        <span class="sale">{{ $product->warranty_month }}M Warranty</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="product-body">
                                                <p class="product-category">{{ $phoneModel?->category?->category_name }}</p>
                                                <h3 class="product-name">
                                                    <a href="{{ route('products.show', $product->id) }}">
                                                        {{ $phoneModel?->model_name }}
                                                    </a>
                                                </h3>
                                                <h4 class="product-price">
                                                    {{ number_format($product->selling_price) }} MMK
                                                </h4>
                                                <div class="product-rating">
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                </div>
                                                <div class="product-btns">
                                                    <button class="quick-view"><a
                                                            href="{{ route('products.show', $product->id) }}"><i
                                                                class="fa fa-eye"></i><span class="tooltipp">quick
                                                                view</span></a></button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /product -->
                                    @endforeach
                                </div>
                                <div id="slick-nav-3" class="products-slick-nav"></div>
                            </div>
                            <!-- /tab -->
                        </div>
                    </div>
                </div>
                <!-- /Products tab & slick -->
            </div>
            <!-- /row -->
        </div>
        <!-- /container -->
    </div>
    <!-- /BEST SELLERS SECTION -->

    <!-- SECTION -->
    <div class="section">
        <!-- container -->
        <div class="container">
            <!-- row -->
            <div class="row">
                <div class="col-md-4 col-xs-6">
                    <div class="section-title">
                        <h4 class="title">New Arrivals</h4>
                        <div class="section-nav">
                            <div id="slick-nav-5" class="products-slick-nav"></div>
                        </div>
                    </div>

                    <div class="products-widget-slick" data-nav="#slick-nav-5">
                        @foreach ($new_products->chunk(3) as $productChunk)
                            <div>
                                @foreach ($productChunk as $product)
                                    <!-- product widget -->
                                    <div class="product-widget" data-id="{{ $product->id }}">
                                        <div class="product-img">
                                            {{-- <img src="{{ $product->imageUrl() }}" alt=""> --}}
                                        </div>
                                        <div class="product-body">
                                            {{-- <p class="product-category">{{ $product->category->name }}</p>
                                            <h3 class="product-name"><a href="">{{ $product->name }}</a></h3>
                                            <h4 class="product-price">
                                                @if ($product->discount_price)
                                                    {{ number_format($product->discount_price) }} MMK
                                                @else
                                                    {{ number_format($product->price) }} MMK
                                                @endif
                                            </h4> --}}
                                        </div>
                                    </div>
                                    <!-- /product widget -->
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-md-4 col-xs-6">
                    <div class="section-title">
                        <h4 class="title">Popular Products</h4>
                        <div class="section-nav">
                            <div id="slick-nav-6" class="products-slick-nav"></div>
                        </div>
                    </div>

                    <div class="products-widget-slick" data-nav="#slick-nav-6">
                        @foreach ($popular_products->chunk(3) as $productChunk)
                            <div>
                                @foreach ($productChunk as $product)
                                    <!-- product widget -->
                                    <div class="product-widget" data-id="{{ $product->id }}">
                                        <div class="product-img">
                                            {{-- <img src="{{ $product->imageUrl() }}" alt=""> --}}
                                        </div>
                                        <div class="product-body">
                                            {{-- <p class="product-category">{{ $product->category->name }}</p>
                                            <h3 class="product-name"><a href="">{{ $product->name }}</a></h3>
                                            <h4 class="product-price">
                                                @if ($product->discount_price)
                                                    {{ number_format($product->discount_price) }} MMK
                                                @else
                                                    {{ number_format($product->price) }} MMK
                                                @endif
                                            </h4> --}}
                                        </div>
                                    </div>
                                    <!-- /product widget -->
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="clearfix visible-sm visible-xs"></div>

                <div class="col-md-4 col-xs-6">
                    <div class="section-title">
                        <h4 class="title">Best Sellers</h4>
                        <div class="section-nav">
                            <div id="slick-nav-7" class="products-slick-nav"></div>
                        </div>
                    </div>

                    <div class="products-widget-slick" data-nav="#slick-nav-7">
                        @foreach ($best_sellers->chunk(3) as $productChunk)
                            <div>
                                @foreach ($productChunk as $product)
                                    <!-- product widget -->
                                    {{-- <div class="product-widget" data-id="{{ $product->id }}">
                                        <div class="product-img">
                                            <img src="{{ $product->imageUrl() }}" alt="">
                                        </div>
                                        <div class="product-body">
                                            <p class="product-category">{{ $product->category->name }}</p>
                                            <h3 class="product-name"><a href="">{{ $product->name }}</a></h3>
                                            <h4 class="product-price">
                                                @if ($product->discount_price)
                                                    {{ number_format($product->discount_price) }} MMK
                                                @else
                                                    {{ number_format($product->price) }} MMK
                                                @endif
                                            </h4>
                                        </div> --}}
                                    </div>
                                    <!-- /product widget -->
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
            <!-- /row -->
        </div>
        <!-- /container -->
    </div>
    <!-- /SECTION -->

    <!-- NEWSLETTER -->
    <div id="newsletter" class="section">
        <!-- container -->
        <div class="container">
            <!-- row -->
            <div class="row">
                <div class="col-md-12">
                    <div class="newsletter">
                        <p>Sign Up for the <strong>NEWSLETTER</strong></p>
                        <form>
                            <input class="input" type="email" placeholder="Enter Your Email">
                            <button class="newsletter-btn"><i class="fa fa-envelope"></i> Subscribe</button>
                        </form>
                        <ul class="newsletter-follow">
                            <li>
                                <a href=""><i class="fa fa-facebook"></i></a>
                            </li>
                            <li>
                                <a href=""><i class="fa fa-twitter"></i></a>
                            </li>
                            <li>
                                <a href=""><i class="fa fa-instagram"></i></a>
                            </li>
                            <li>
                                <a href=""><i class="fa fa-pinterest"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /row -->
        </div>
        <!-- /container -->
    </div>
    <!-- /NEWSLETTER -->
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('.product-widget').on('click', function() {
                const productId = $(this).data('id');
                const route = "{{ route('products.show', ':id') }}".replace(':id', productId);
                window.location.href = route;
            });
        });
    </script>
@endsection
