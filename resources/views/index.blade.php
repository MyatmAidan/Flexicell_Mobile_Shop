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

        .scroll-card-item {
            animation: fadeInUp .45s ease both;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Skeleton loader */
        .skeleton-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #eee;
            margin-bottom: 30px;
        }
        .skeleton-img {
            height: 250px;
            background: linear-gradient(90deg, #f0f0f0 25%, #e8e8e8 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.4s infinite;
        }
        .skeleton-body { padding: 20px; }
        .skeleton-line {
            height: 14px;
            border-radius: 4px;
            background: linear-gradient(90deg, #f0f0f0 25%, #e8e8e8 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.4s infinite;
            margin-bottom: 10px;
        }
        .skeleton-line.w50 { width: 50%; }
        .skeleton-line.w70 { width: 70%; }
        .skeleton-line.w40 { width: 40%; height: 20px; }
        .skeleton-line.w100 { width: 100%; height: 38px; border-radius: 6px; margin-top: 8px; }
        @keyframes shimmer {
            0%   { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Load more button */
        .load-more-btn {
            display: inline-block;
            padding: 10px 36px;
            border: 2px solid #D10024;
            background: transparent;
            color: #D10024;
            font-weight: 600;
            font-size: 14px;
            border-radius: 30px;
            cursor: pointer;
            transition: all .25s ease;
            letter-spacing: .5px;
        }
        .load-more-btn:hover {
            background: #D10024;
            color: #fff;
        }
        .load-more-btn:disabled {
            opacity: .5;
            cursor: not-allowed;
        }
        .load-more-btn .spinner-border {
            width: 16px;
            height: 16px;
            border-width: 2px;
            margin-right: 6px;
            vertical-align: middle;
        }

        /* Counter badge */
        .scroll-counter {
            font-size: 13px;
            color: #888;
            margin-bottom: 8px;
        }
        .scroll-counter strong { color: #D10024; }
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

    <!-- NEW ARRIVALS SECTION (scroll pagination) -->
    <div class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-title d-flex flex-wrap justify-content-between align-items-end">
                        <h3 class="title mb-0">New Arrivals</h3>
                        <div class="scroll-counter" id="newArrivalsCounter"></div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="row" id="newArrivalsGrid"></div>
                    <div class="row" id="newArrivalsSkeleton">
                        <div class="col-md-3 col-sm-6 col-xs-12"><div class="skeleton-card"><div class="skeleton-img"></div><div class="skeleton-body"><div class="skeleton-line w50"></div><div class="skeleton-line w70"></div><div class="skeleton-line w40"></div><div class="skeleton-line w100"></div></div></div></div>
                        <div class="col-md-3 col-sm-6 col-xs-12"><div class="skeleton-card"><div class="skeleton-img"></div><div class="skeleton-body"><div class="skeleton-line w50"></div><div class="skeleton-line w70"></div><div class="skeleton-line w40"></div><div class="skeleton-line w100"></div></div></div></div>
                        <div class="col-md-3 col-sm-6 col-xs-12"><div class="skeleton-card"><div class="skeleton-img"></div><div class="skeleton-body"><div class="skeleton-line w50"></div><div class="skeleton-line w70"></div><div class="skeleton-line w40"></div><div class="skeleton-line w100"></div></div></div></div>
                        <div class="col-md-3 col-sm-6 col-xs-12"><div class="skeleton-card"><div class="skeleton-img"></div><div class="skeleton-body"><div class="skeleton-line w50"></div><div class="skeleton-line w70"></div><div class="skeleton-line w40"></div><div class="skeleton-line w100"></div></div></div></div>
                    </div>
                    <div class="text-center" style="margin-top:15px;" id="newArrivalsActions">
                        <button class="load-more-btn" id="newArrivalsLoadMore" style="display:none;">Load More</button>
                    </div>
                    <div class="text-center py-2" id="newArrivalsEnd" style="display:none;">
                        <span class="text-muted" style="font-size:13px;">— All products loaded —</span>
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

    <!-- BLOG SECTION (scroll pagination) -->
    <div class="section" id="blogSection">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-title d-flex flex-wrap justify-content-between align-items-end gap-2">
                        <h3 class="title mb-0">Latest from the Blog</h3>
                        <div class="d-flex align-items-center gap-3">
                            <div class="scroll-counter" id="blogCounter"></div>
                            <a href="{{ route('blogs.index') }}" class="btn btn-sm btn-outline-dark" style="border-radius: 4px;">
                                View all <i class="fa fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" id="blogGrid"></div>
            <div class="row" id="blogSkeleton">
                <div class="col-md-4 col-sm-6 mb-4"><div class="skeleton-card"><div class="skeleton-img" style="height:180px"></div><div class="skeleton-body"><div class="skeleton-line w50"></div><div class="skeleton-line w70"></div><div class="skeleton-line w40"></div></div></div></div>
                <div class="col-md-4 col-sm-6 mb-4"><div class="skeleton-card"><div class="skeleton-img" style="height:180px"></div><div class="skeleton-body"><div class="skeleton-line w50"></div><div class="skeleton-line w70"></div><div class="skeleton-line w40"></div></div></div></div>
                <div class="col-md-4 col-sm-6 mb-4"><div class="skeleton-card"><div class="skeleton-img" style="height:180px"></div><div class="skeleton-body"><div class="skeleton-line w50"></div><div class="skeleton-line w70"></div><div class="skeleton-line w40"></div></div></div></div>
            </div>
            <div class="text-center" style="margin-top:15px;">
                <button class="load-more-btn" id="blogLoadMore" style="display:none;">Load More</button>
            </div>
            <div class="text-center py-2" id="blogEnd" style="display:none;">
                <span class="text-muted" style="font-size:13px;">— All posts loaded —</span>
            </div>
        </div>
    </div>
    <!-- /BLOG SECTION -->

    <!-- POPULAR PRODUCTS SECTION (scroll pagination) -->
    <div class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-title d-flex flex-wrap justify-content-between align-items-end">
                        <h3 class="title mb-0">Popular Products</h3>
                        <div class="scroll-counter" id="popularCounter"></div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="row" id="popularGrid"></div>
                    <div class="row" id="popularSkeleton">
                        <div class="col-md-3 col-sm-6 col-xs-12"><div class="skeleton-card"><div class="skeleton-img"></div><div class="skeleton-body"><div class="skeleton-line w50"></div><div class="skeleton-line w70"></div><div class="skeleton-line w40"></div><div class="skeleton-line w100"></div></div></div></div>
                        <div class="col-md-3 col-sm-6 col-xs-12"><div class="skeleton-card"><div class="skeleton-img"></div><div class="skeleton-body"><div class="skeleton-line w50"></div><div class="skeleton-line w70"></div><div class="skeleton-line w40"></div><div class="skeleton-line w100"></div></div></div></div>
                        <div class="col-md-3 col-sm-6 col-xs-12"><div class="skeleton-card"><div class="skeleton-img"></div><div class="skeleton-body"><div class="skeleton-line w50"></div><div class="skeleton-line w70"></div><div class="skeleton-line w40"></div><div class="skeleton-line w100"></div></div></div></div>
                        <div class="col-md-3 col-sm-6 col-xs-12"><div class="skeleton-card"><div class="skeleton-img"></div><div class="skeleton-body"><div class="skeleton-line w50"></div><div class="skeleton-line w70"></div><div class="skeleton-line w40"></div><div class="skeleton-line w100"></div></div></div></div>
                    </div>
                    <div class="text-center" style="margin-top:15px;" id="popularActions">
                        <button class="load-more-btn" id="popularLoadMore" style="display:none;">Load More</button>
                    </div>
                    <div class="text-center py-2" id="popularEnd" style="display:none;">
                        <span class="text-muted" style="font-size:13px;">— All products loaded —</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- BEST SELLERS SECTION (scroll pagination) -->
    <div class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-title d-flex flex-wrap justify-content-between align-items-end">
                        <h3 class="title mb-0">Best Sellers</h3>
                        <div class="scroll-counter" id="bestSellersCounter"></div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="row" id="bestSellersGrid"></div>
                    <div class="row" id="bestSellersSkeleton">
                        <div class="col-md-3 col-sm-6 col-xs-12"><div class="skeleton-card"><div class="skeleton-img"></div><div class="skeleton-body"><div class="skeleton-line w50"></div><div class="skeleton-line w70"></div><div class="skeleton-line w40"></div><div class="skeleton-line w100"></div></div></div></div>
                        <div class="col-md-3 col-sm-6 col-xs-12"><div class="skeleton-card"><div class="skeleton-img"></div><div class="skeleton-body"><div class="skeleton-line w50"></div><div class="skeleton-line w70"></div><div class="skeleton-line w40"></div><div class="skeleton-line w100"></div></div></div></div>
                        <div class="col-md-3 col-sm-6 col-xs-12"><div class="skeleton-card"><div class="skeleton-img"></div><div class="skeleton-body"><div class="skeleton-line w50"></div><div class="skeleton-line w70"></div><div class="skeleton-line w40"></div><div class="skeleton-line w100"></div></div></div></div>
                        <div class="col-md-3 col-sm-6 col-xs-12"><div class="skeleton-card"><div class="skeleton-img"></div><div class="skeleton-body"><div class="skeleton-line w50"></div><div class="skeleton-line w70"></div><div class="skeleton-line w40"></div><div class="skeleton-line w100"></div></div></div></div>
                    </div>
                    <div class="text-center" style="margin-top:15px;">
                        <button class="load-more-btn" id="bestSellersLoadMore" style="display:none;">Load More</button>
                    </div>
                    <div class="text-center py-2" id="bestSellersEnd" style="display:none;">
                        <span class="text-muted" style="font-size:13px;">— All products loaded —</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /BEST SELLERS -->

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

            /* ── Product card builder ── */
            function buildProductCard(p, idx) {
                var img = p.image
                    ? '<img src="'+p.image+'" alt="'+p.model_name+'">'
                    : '<div class="text-muted">No Image</div>';
                var badge = p.is_sold_out ? '<div class="sold-out-overlay">Sold Out</div>' : '';
                var logo  = p.brand_logo
                    ? '<img src="'+p.brand_logo+'" alt="" class="brand-logo-small">'
                    : '';

                return '<div class="col-md-3 col-sm-6 col-xs-12 scroll-card-item" style="animation-delay:'+(idx * 0.08)+'s">' +
                    '<div class="product-card">' +
                        '<div class="product-img-container">' + badge + logo + img + '</div>' +
                        '<div class="product-info">' +
                            '<p class="product-category-text">'+p.category_name+'</p>' +
                            '<h3 class="product-name-text"><a href="'+p.url+'">'+p.model_name+'</a></h3>' +
                            '<div class="product-price-text">'+Number(p.selling_price).toLocaleString()+' Ks</div>' +
                            '<div class="product-btns">' +
                                '<a href="'+p.url+'" class="btn btn-block" style="background:#1e1f29;color:#fff;border-radius:6px;">View Details</a>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';
            }

            /* ── Blog card builder ── */
            function buildBlogCard(b, idx) {
                var thumb = b.thumbnail
                    ? '<img src="'+b.thumbnail+'" alt="'+b.title+'" class="home-blog-card-img">'
                    : '<div class="home-blog-card-img d-flex align-items-center justify-content-center bg-light"><i class="fa fa-newspaper-o fa-3x text-muted"></i></div>';

                return '<div class="col-md-4 col-sm-6 mb-4 scroll-card-item" style="animation-delay:'+(idx * 0.1)+'s">' +
                    '<div class="home-blog-card">' +
                        thumb +
                        '<div class="home-blog-card-body">' +
                            '<div class="home-blog-card-meta">' +
                                '<i class="fa fa-calendar-o"></i> '+b.date+' &middot; '+b.sections_count+' '+b.sections_label +
                            '</div>' +
                            '<div class="home-blog-card-title">'+b.title+'</div>' +
                            '<a href="'+b.url+'" class="home-blog-read">Read more <i class="fa fa-arrow-right"></i></a>' +
                        '</div>' +
                    '</div>' +
                '</div>';
            }

            /* ── Generic scroller factory ── */
            function createScroller(opts) {
                var page      = 0;
                var loaded    = 0;
                var total     = 0;
                var loading   = false;
                var finished  = false;

                var $grid    = $('#' + opts.gridId);
                var $skel    = $('#' + opts.skeletonId);
                var $end     = $('#' + opts.endId);
                var $btn     = $('#' + opts.btnId);
                var $counter = opts.counterId ? $('#' + opts.counterId) : null;
                var label    = opts.label || 'products';

                function updateCounter() {
                    if ($counter && total > 0) {
                        $counter.html('Showing <strong>' + loaded + '</strong> of <strong>' + total + '</strong> ' + label);
                    }
                }

                function loadMore() {
                    if (loading || finished) return;
                    loading = true;
                    page++;

                    $skel.show();
                    $btn.prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm text-danger"></span> Loading...'
                    );

                    $.get(opts.apiUrl, $.extend({ page: page }, opts.params || {}), function(res) {
                        $skel.hide();
                        if (res.total !== undefined) total = res.total;

                        if (res.data && res.data.length) {
                            var html = '';
                            res.data.forEach(function(item, i) {
                                html += opts.cardBuilder(item, i);
                            });
                            $grid.append(html);
                            loaded += res.data.length;
                        }

                        updateCounter();

                        if (!res.has_more) {
                            finished = true;
                            $btn.hide();
                            if (loaded > 0) $end.show();
                        } else {
                            $btn.show().prop('disabled', false).text('Load More');
                        }
                        loading = false;
                    }).fail(function() {
                        loading = false;
                        $skel.hide();
                        $btn.show().prop('disabled', false).text('Retry');
                    });
                }

                $btn.on('click', function() { loadMore(); });

                loadMore();

                $(window).on('scroll', function() {
                    if (finished || loading) return;
                    var btnTop = $btn.offset() ? $btn.offset().top : 0;
                    if (btnTop && $(window).scrollTop() + $(window).height() >= btnTop - 150) {
                        loadMore();
                    }
                });
            }

            /* ── Initialise all scrollers ── */
            var productApiUrl = "{{ route('api.products.scroll') }}";
            var blogApiUrl    = "{{ route('api.blogs.scroll') }}";

            createScroller({
                gridId: 'newArrivalsGrid', skeletonId: 'newArrivalsSkeleton',
                endId: 'newArrivalsEnd', btnId: 'newArrivalsLoadMore',
                counterId: 'newArrivalsCounter', label: 'products',
                apiUrl: productApiUrl, params: { type: 'new' },
                cardBuilder: buildProductCard
            });

            createScroller({
                gridId: 'blogGrid', skeletonId: 'blogSkeleton',
                endId: 'blogEnd', btnId: 'blogLoadMore',
                counterId: 'blogCounter', label: 'posts',
                apiUrl: blogApiUrl, params: {},
                cardBuilder: buildBlogCard
            });

            createScroller({
                gridId: 'popularGrid', skeletonId: 'popularSkeleton',
                endId: 'popularEnd', btnId: 'popularLoadMore',
                counterId: 'popularCounter', label: 'products',
                apiUrl: productApiUrl, params: { type: 'popular' },
                cardBuilder: buildProductCard
            });

            createScroller({
                gridId: 'bestSellersGrid', skeletonId: 'bestSellersSkeleton',
                endId: 'bestSellersEnd', btnId: 'bestSellersLoadMore',
                counterId: 'bestSellersCounter', label: 'products',
                apiUrl: productApiUrl, params: { type: 'bestseller' },
                cardBuilder: buildProductCard
            });
        });
    </script>
@endsection
