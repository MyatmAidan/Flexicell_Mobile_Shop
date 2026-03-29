@extends('layouts.user')

@section('title', $product->phoneModel?->model_name . ' - Details')

@section('style')
    <style>
        .product-detail-container {
            background: #fff;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            margin-bottom: 50px;
        }
        .swiper-main {
            height: 450px;
            border-radius: 12px;
            overflow: hidden;
            background: #f8f9fa;
        }
        .swiper-thumb {
            height: 80px;
            margin-top: 15px;
        }
        .swiper-slide img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .product-detail-title {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        .product-detail-price {
            font-size: 28px;
            font-weight: 700;
            color: #D10024;
            margin-bottom: 25px;
        }
        .detail-label {
            font-weight: 600;
            color: #888;
            text-transform: uppercase;
            font-size: 13px;
            margin-bottom: 5px;
        }
        .detail-value {
            font-size: 16px;
            color: #333;
            margin-bottom: 20px;
        }
        .color-swatches {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
        }
        .color-dot-large {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 1px #ddd;
        }
        .spec-table {
            margin-top: 30px;
        }
        .spec-table th {
            background: #f8f9fa;
            width: 30%;
        }
        .back-btn {
            background: #1e1f29;
            color: #fff;
            border-radius: 8px;
            padding: 10px 25px;
            transition: 0.3s;
        }
        .back-btn:hover {
            background: #D10024;
            color: #fff;
        }
    </style>
@endsection

@section('content')
    <div class="section">
        <div class="container">
            <div class="mb-4" style="display: flex; justify-content: space-between; align-items: center;">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb" style="background: transparent; padding: 0; margin: 0;">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('products.search') }}">Products</a></li>
                        <li class="breadcrumb-item active">{{ $product->phoneModel?->model_name }}</li>
                    </ol>
                </nav>
                <button onclick="history.back()" class="btn back-btn">
                    <i class="fa fa-arrow-left"></i> Back
                </button>
            </div>

            <div class="product-detail-container">
                <div class="row">
                    <div class="col-md-5">
                        <div class="swiper mySwiper2 swiper-main">
                            <div class="swiper-wrapper">
                                @php
                                    $images = is_array($product->image) ? $product->image : (array) json_decode($product->image, true);
                                @endphp
                                @foreach ($images as $image)
                                    <div class="swiper-slide">
                                        <img src="{{ asset('storage/products/' . $image) }}" alt="">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div thumbsSlider="" class="swiper mySwiper swiper-thumb">
                            <div class="swiper-wrapper">
                                @foreach ($images as $image)
                                    <div class="swiper-slide" style="cursor: pointer; border-radius: 8px; overflow: hidden; border: 1px solid #eee;">
                                        <img src="{{ asset('storage/products/' . $image) }}" alt="">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7" style="padding-left: 40px;">
                        @php $phoneModel = $product->phoneModel; @endphp
                        
                        <div class="detail-label">{{ $phoneModel?->brand?->brand_name }} | {{ $phoneModel?->category?->category_name }}</div>
                        <h1 class="product-detail-title">{{ $phoneModel?->model_name }}</h1>
                        
                        <div class="product-detail-price">
                            {{ number_format($product->selling_price, 0) }} Ks
                        </div>

                        <div class="detail-label">Available Colors</div>
                        <div class="color-swatches">
                            @php $availableColors = $phoneModel->available_color ?? []; @endphp
                            @foreach($availableColors as $color)
                                <span class="color-dot-large" style="background-color: {{ $color['value'] }}" title="{{ $color['name'] }}"></span>
                            @endforeach
                            @if(empty($availableColors))
                                <span class="text-muted small">Standard</span>
                            @endif
                        </div>

                        <div class="detail-label">Stock Status</div>
                        <div class="detail-value text-success">
                            <i class="fa fa-check-circle"></i> {{ $product->stock_quantity }} items available
                        </div>

                        <div class="detail-label">Description</div>
                        <div class="detail-value" style="line-height: 1.6;">
                            {{ $product->description ?: 'No description available for this product.' }}
                        </div>

                        <h4 style="margin-top: 40px; font-weight: 700;">Technical Specifications</h4>
                        <table class="table table-bordered spec-table">
                            <tbody>
                                @if (is_array($phoneModel?->description))
                                    @foreach ($phoneModel->description as $spec)
                                        <tr>
                                            <th>{{ $spec['key'] ?? '' }}</th>
                                            <td>{{ $spec['value'] ?? '' }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">No specifications listed.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var swiper = new Swiper(".mySwiper", {
            spaceBetween: 10,
            slidesPerView: 4,
            freeMode: true,
            watchSlidesProgress: true,
        });
        var swiper2 = new Swiper(".mySwiper2", {
            spaceBetween: 10,
            loop: true,
            thumbs: {
                swiper: swiper,
            },
        });
        $.ajax({
            url: "{{ route('products.updateView') }}",
            type: "POST",
            data: {
                product_id: {{ $product->id }},
                _token: '{{ csrf_token() }}'
            }
        })
    </script>
@endsection
