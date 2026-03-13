@extends('layouts.user')
@section('title', 'Product Details')
@section('style')
    <style>
        .swiper-slide {
            background-size: cover;
            background-position: center;
        }

        .mySwiper2 {
            height: 80%;
            width: 100%;
        }

        .mySwiper {
            height: 20%;
            box-sizing: border-box;
            padding: 10px 0;
        }

        .mySwiper .swiper-slide {
            width: 25%;
            height: 100%;
            opacity: 0.4;
        }

        .mySwiper .swiper-slide-thumb-active {
            opacity: 1;
        }

        .swiper-slide img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
@endsection
@section('content')
    <div class="section">
        <div class="container" style="min-height: 100vh">
            <div class="mb-3" style="display: flex; justify-content: end;">
                <button onclick="history.back()" class="btn btn-primary">
                    Back
                </button>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <!-- Swiper -->
                    <div style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff" class="swiper mySwiper2">
                        <div class="swiper-wrapper">
                            @php
                                $images = is_array($product->image) ? $product->image : (array) json_decode($product->image, true);
                            @endphp
                            @foreach ($images as $image)
                                <div class="swiper-slide">
                                    <img src="{{ asset('storage/products/' . $image) }}" class="d-block w-100"
                                        alt="{{ $product->phoneModel?->model_name }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div thumbsSlider="" class="swiper mySwiper mt-2">
                        <div class="swiper-wrapper">
                            @foreach ($images as $image)
                                <div class="swiper-slide">
                                    <img src="{{ asset('storage/products/' . $image) }}" class="d-block w-100"
                                        alt="{{ $product->phoneModel?->model_name }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    @php
                        $phoneModel = $product->phoneModel;
                    @endphp
                    <p><strong>Brand:</strong> {{ $phoneModel?->brand?->brand_name }}</p>
                    <p><strong>Category:</strong> {{ $phoneModel?->category?->category_name }}</p>
                    <p><strong>Model:</strong> {{ $phoneModel?->model_name }}</p>
                    <p><strong>Description:</strong> {{ $product->description }}</p>
                    <p><strong>Current Price:</strong> {{ number_format($product->selling_price, 0) }} MMK</p>
                    <p><strong>Stock:</strong> {{ $product->stock_quantity }}</p>
                    <h4 style="margin-top: 20px;">Specifications:</h4>
                    <table class="table table-striped table-bordered">
                        <tbody>
                            @if (is_array($phoneModel?->description))
                                @foreach ($phoneModel->description as $spec)
                                    <tr>
                                        <td class="col-4">
                                            <strong>{{ $spec['key'] ?? '' }}</strong>
                                        </td>
                                        <td class="col-8">{{ $spec['value'] ?? '' }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var swiper = new Swiper(".mySwiper", {
            spaceBetween: 10,
            slidesPerView: 5,
            freeMode: true,
            watchSlidesProgress: true,
        });
        var swiper2 = new Swiper(".mySwiper2", {
            spaceBetween: 10,
            loop: true,
            autoplay: {
                delay: 3500,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
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
