@extends('layouts.user')
@section('title', 'Home')
@section('style')
    <style>
        .product-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid #eee;
            margin-bottom: 30px;
            position: relative;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .product-img-container {
            position: relative;
            height: 250px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .product-img-container img {
            max-height: 100%;
            object-fit: contain;
        }
        .brand-logo-small {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 35px;
            height: 35px;
            background: #fff;
            border-radius: 8px;
            padding: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            z-index: 2;
        }
        .sold-out-overlay {
            position: absolute;
            top: 15px;
            left: 15px;
            background: #000;
            color: #fff;
            padding: 5px 12px;
            font-size: 11px;
            font-weight: 700;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            z-index: 2;
        }
        .product-info {
            padding: 20px;
        }
        .product-category-text {
            font-size: 12px;
            color: #888;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-weight: 500;
        }
        .product-name-text {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .product-price-text {
            font-size: 18px;
            font-weight: 700;
            color: #D10024;
            margin-bottom: 15px;
        }
        .color-swatches {
            display: flex;
            gap: 6px;
            margin-bottom: 15px;
        }
        .color-swatch-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 1px solid #ddd;
        }
        .section-title .title {
            position: relative;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .section-title .title:after {
            content: "";
            position: absolute;
            left: 0;
            bottom: 0;
            height: 3px;
            width: 50px;
            background-color: #D10024;
        }
        .home-blog-card {
            border: 1px solid #e4e4e4;
            border-radius: 8px;
            overflow: hidden;
            transition: box-shadow .2s;
            background: #fff;
            height: 100%;
        }
        .home-blog-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.12); }
        .home-blog-card-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .home-blog-card-body { padding: 14px; }
        .home-blog-card-title {
            font-size: 15px;
            font-weight: 600;
            color: #333;
            margin-bottom: 6px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.6em;
        }
        .home-blog-card-meta { font-size: 12px; color: #999; margin-bottom: 10px; }
        .home-blog-read {
            background: #D10024;
            color: #fff;
            border-radius: 4px;
            padding: 6px 14px;
            font-size: 13px;
            text-decoration: none;
            display: inline-block;
        }
        .home-blog-read:hover { background: #b8001f; color: #fff; }
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

    <!-- NEW ARRIVALS SECTION -->
    <div class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-title">
                        <h3 class="title">New Arrivals</h3>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="row">
                        @foreach ($new_products as $product)
                            @php
                                $phoneModel = $product->phoneModel;
                                $brand = $phoneModel?->brand;
                                $images = is_array($product->image) ? $product->image : (array) json_decode($product->image, true);
                                $firstImage = $images[0] ?? null;
                                $availableColors = $phoneModel->available_color ?? [];
                                $isSoldOut = ($product->product_type === 'new' && $product->stock_quantity <= 0) ||
                                             ($product->product_type !== 'new' && $product->devices()->where('status', 'available')->count() <= 0);
                            @endphp

                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="product-card">
                                    <div class="product-img-container">
                                        @if ($isSoldOut)
                                            <div class="sold-out-overlay">Sold Out</div>
                                        @endif

                                        @if ($brand && $brand->logo)
                                            <img src="{{ $brand->logoUrl() }}" alt="" class="brand-logo-small">
                                        @endif

                                        @if ($firstImage)
                                            <img src="{{ asset('storage/products/' . $firstImage) }}" alt="{{ $phoneModel?->model_name }}">
                                        @else
                                            <div class="text-muted">No Image</div>
                                        @endif
                                    </div>

                                    <div class="product-info">
                                        <p class="product-category-text">{{ $phoneModel?->category?->category_name ?? 'Gadget' }}</p>
                                        <h3 class="product-name-text">
                                            <a href="{{ route('products.show', $product->id) }}">
                                                {{ $phoneModel?->model_name }}
                                            </a>
                                        </h3>

                                        <div class="color-swatches">
                                            @foreach($availableColors as $color)
                                                <span class="color-swatch-dot" style="background-color: {{ $color['value'] }}" title="{{ $color['name'] }}"></span>
                                            @endforeach
                                        </div>

                                        <div class="product-price-text">
                                            {{ number_format($product->selling_price) }} Ks
                                        </div>

                                        <div class="product-btns">
                                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-block" style="background: #1e1f29; color: #fff; border-radius: 6px;">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    <!-- BLOG SECTION -->
    @if(isset($blogs) && $blogs->isNotEmpty())
    <div class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-title d-flex flex-wrap justify-content-between align-items-end gap-2">
                        <h3 class="title mb-0">Latest from the blog</h3>
                        <a href="{{ route('blogs.index') }}" class="btn btn-sm btn-outline-dark" style="border-radius: 4px;">
                            View all <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                @foreach ($blogs as $blog)
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="home-blog-card">
                            @if ($blog->thumbnail)
                                <img src="{{ asset('storage/blogs/' . $blog->thumbnail) }}"
                                    alt="{{ $blog->title }}" class="home-blog-card-img">
                            @else
                                <div class="home-blog-card-img d-flex align-items-center justify-content-center bg-light">
                                    <i class="fa fa-newspaper-o fa-3x text-muted"></i>
                                </div>
                            @endif
                            <div class="home-blog-card-body">
                                <div class="home-blog-card-meta">
                                    <i class="fa fa-calendar-o"></i>
                                    {{ $blog->created_at->format('M d, Y') }}
                                    &nbsp;&middot;&nbsp;
                                    {{ $blog->contents_count }} {{ $blog->contents_count === 1 ? 'section' : 'sections' }}
                                </div>
                                <div class="home-blog-card-title">{{ $blog->title }}</div>
                                <a href="{{ route('blogs.show', $blog) }}" class="home-blog-read">
                                    Read more <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    <!-- /BLOG SECTION -->

    <!-- POPULAR PRODUCTS SECTION -->
    <div class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-title">
                        <h3 class="title">Popular Products</h3>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="row">
                        @foreach ($popular_products as $product)
                            @php
                                $phoneModel = $product->phoneModel;
                                $brand = $phoneModel?->brand;
                                $images = is_array($product->image) ? $product->image : (array) json_decode($product->image, true);
                                $firstImage = $images[0] ?? null;
                                $availableColors = $phoneModel->available_color ?? [];
                                $isSoldOut = ($product->product_type === 'new' && $product->stock_quantity <= 0) ||
                                             ($product->product_type !== 'new' && $product->devices()->where('status', 'available')->count() <= 0);
                            @endphp

                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="product-card">
                                    <div class="product-img-container">
                                        @if ($isSoldOut)
                                            <div class="sold-out-overlay">Sold Out</div>
                                        @endif

                                        @if ($brand && $brand->logo)
                                            <img src="{{ $brand->logoUrl() }}" alt="" class="brand-logo-small">
                                        @endif

                                        @if ($firstImage)
                                            <img src="{{ asset('storage/products/' . $firstImage) }}" alt="{{ $phoneModel?->model_name }}">
                                        @else
                                            <div class="text-muted">No Image</div>
                                        @endif
                                    </div>

                                    <div class="product-info">
                                        <p class="product-category-text">{{ $phoneModel?->category?->category_name ?? 'Gadget' }}</p>
                                        <h3 class="product-name-text">
                                            <a href="{{ route('products.show', $product->id) }}">
                                                {{ $phoneModel?->model_name }}
                                            </a>
                                        </h3>

                                        <div class="color-swatches">
                                            @foreach($availableColors as $color)
                                                <span class="color-swatch-dot" style="background-color: {{ $color['value'] }}" title="{{ $color['name'] }}"></span>
                                            @endforeach
                                        </div>

                                        <div class="product-price-text">
                                            {{ number_format($product->selling_price) }} Ks
                                        </div>

                                        <div class="product-btns">
                                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-block" style="background: #1e1f29; color: #fff; border-radius: 6px;">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- BEST SELLERS SECTION -->
    <div class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-title">
                        <h3 class="title">Best Sellers</h3>
                    </div>
                </div>
            </div>
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
                                            <img src="{{ $product->imageUrl() }}" alt="">
                                        </div>
                                        <div class="product-body">
                                            <p class="product-category">{{ $product->phoneModel->category->category_name ?? 'N/A' }}</p>
                                            <h3 class="product-name"><a href="{{ route('products.show', $product->id) }}">{{ $product->phoneModel->model_name ?? 'N/A' }}</a></h3>
                                            <h4 class="product-price">
                                                {{ number_format($product->selling_price) }} MMK
                                            </h4>
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
                                            <img src="{{ $product->imageUrl() }}" alt="">
                                        </div>
                                        <div class="product-body">
                                            <p class="product-category">{{ $product->phoneModel->category->category_name ?? 'N/A' }}</p>
                                            <h3 class="product-name"><a href="{{ route('products.show', $product->id) }}">{{ $product->phoneModel->model_name ?? 'N/A' }}</a></h3>
                                            <h4 class="product-price">
                                                {{ number_format($product->selling_price) }} MMK
                                            </h4>
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
                                    <div class="product-widget" data-id="{{ $product->id }}">
                                        <div class="product-img">
                                            <img src="{{ $product->imageUrl() }}" alt="">
                                        </div>
                                        <div class="product-body">
                                            <p class="product-category">{{ $product->phoneModel->category->category_name ?? 'N/A' }}</p>
                                            <h3 class="product-name"><a href="{{ route('products.show', $product->id) }}">{{ $product->phoneModel->model_name ?? 'N/A' }}</a></h3>
                                            <h4 class="product-price">
                                                {{ number_format($product->selling_price) }} MMK
                                            </h4>
                                        </div>
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
                                <a href="https://www.facebook.com/hmt.yoh.154"><i class="fa fa-facebook"></i></a>
                            </li>
                            <li>
                                <a href="https://twitter.com/Myatm_Aidan"><i class="fa fa-twitter"></i></a>
                            </li>
                            <li>
                                <a href="https://www.instagram.com/Myatm_Aidan"><i class="fa fa-instagram"></i></a>
                            </li>
                            <li>
                                <a href="https://www.pinterest.com/Myatm_Aidan"><i class="fa fa-pinterest"></i></a>
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
