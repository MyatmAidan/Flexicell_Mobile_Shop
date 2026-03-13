@extends('layouts.user')
@section('title', 'Search Results')
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
        <div class="container" style="min-height: 100vh">
            <!-- row -->
            <div class="row">
                <!-- section title -->
                <div class="col-md-12">
                    <div class="section-title">
                        <h3 class="title">Search Result ({{ $products->count() }})</h3>
                    </div>
                </div>
                <!-- /section title -->
                <!-- Products tab & slick -->
                <div class="col-md-12">
                    <div class="row">
                        <div class="products-tabs">
                            <!-- tab -->
                            @if ($products->isEmpty())
                                <div class="col-md-12 mt-5">
                                    <h2 class="text-center text-danger">No products found.</h2>
                                </div>
                            @endif
                            <div id="tab1" class="tab-pane active">
                                <div class="products-slick" data-nav="#slick-nav-1">
                                    @foreach ($products as $product)
                                        @php
                                            $phoneModel = $product->phoneModel;
                                            $brand = $phoneModel?->brand;
                                            $images = is_array($product->image) ? $product->image : (array) json_decode($product->image, true);
                                            $firstImage = $images[0] ?? null;
                                        @endphp
                                        <!-- product -->
                                        <div class="product">
                                            <div class="product-img">
                                                @if ($brand && $brand->logo)
                                                    <img src="{{ $brand->logoUrl() }}"
                                                        alt="{{ $brand->brand_name ?? '' }}" class="brand">
                                                @endif
                                                @if ($firstImage)
                                                    <img src="{{ asset('storage/products/' . $firstImage) }}" alt="">
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
@endsection
