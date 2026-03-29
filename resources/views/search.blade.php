@extends('layouts.user')

@section('title', 'Search Results')

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
        .sidebar-widget {
            margin-bottom: 40px;
        }
        .widget-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #D10024;
            display: inline-block;
        }
        .filter-list {
            list-style: none;
            padding: 0;
        }
        .filter-list li {
            margin-bottom: 10px;
        }
        .filter-list a {
            color: #555;
            transition: 0.2s;
            display: flex;
            justify-content: space-between;
        }
        .filter-list a:hover, .filter-list a.active {
            color: #D10024;
            padding-left: 5px;
        }
        .count-badge {
            background: #f0f0f0;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
            color: #777;
        }
    </style>
@endsection

@section('content')
    <div class="section">
        <div class="container" style="min-height: 100vh">
            <div class="row">
                <!-- SIDEBAR -->
                <div id="aside" class="col-md-3">
                    <div class="sidebar-widget">
                        <h3 class="widget-title">Categories</h3>
                        <ul class="filter-list">
                            <li>
                                <a href="{{ route('products.search', ['query' => request('query')]) }}" 
                                   class="{{ !request('category_id') ? 'active' : '' }}">
                                    All Categories
                                </a>
                            </li>
                            @foreach($categories as $cat)
                                <li>
                                    <a href="{{ route('products.search', ['category_id' => $cat->id, 'query' => request('query'), 'brand_id' => request('brand_id')]) }}"
                                       class="{{ request('category_id') == $cat->id ? 'active' : '' }}">
                                        {{ $cat->category_name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="sidebar-widget">
                        <h3 class="widget-title">Brands</h3>
                        <ul class="filter-list">
                            <li>
                                <a href="{{ route('products.search', ['query' => request('query'), 'category_id' => request('category_id')]) }}"
                                   class="{{ !request('brand_id') ? 'active' : '' }}">
                                    All Brands
                                </a>
                            </li>
                            @foreach($brands as $brand)
                                <li>
                                    <a href="{{ route('products.search', ['brand_id' => $brand->id, 'query' => request('query'), 'category_id' => request('category_id')]) }}"
                                       class="{{ request('brand_id') == $brand->id ? 'active' : '' }}">
                                        {{ $brand->brand_name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <!-- /SIDEBAR -->

                <!-- STORE -->
                <div id="store" class="col-md-9">
                    <div class="store-filter clearfix">
                        <div class="pull-left">
                            <h3 class="title" style="margin-top: 0">
                                @if(request('query'))
                                    Results for "{{ request('query') }}"
                                @else
                                    All Products
                                @endif
                                <small class="text-muted">({{ $products->count() }} items)</small>
                            </h3>
                        </div>
                    </div>

                    <div class="row">
                        @if ($products->isEmpty())
                            <div class="col-md-12 mt-5">
                                <h2 class="text-center text-muted">No products found.</h2>
                                <p class="text-center">Try adjusting your filters or search query.</p>
                            </div>
                        @endif

                        @foreach ($products as $product)
                            @php
                                $phoneModel = $product->phoneModel;
                                $brand = $phoneModel?->brand;
                                $images = is_array($product->image) ? $product->image : (array) json_decode($product->image, true);
                                $firstImage = $images[0] ?? null;
                                $availableColors = $phoneModel->available_color ?? [];
                                $isSoldOut = ($product->product_type === 'new' && $product->stock_quantity <= 0) || 
                                             ($product->product_type !== 'new' && $product->devices()->where('status', 'available')->count() <= 0);
                            @endphp
                            
                            <div class="col-md-4 col-sm-6 col-xs-12">
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
                <!-- /STORE -->
            </div>
        </div>
    </div>
@endsection
