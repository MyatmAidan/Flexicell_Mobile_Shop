@extends('layouts.app')

@section('meta')
    <meta name="description" content="Phone Model Show">
    <meta name="keywords" content="Phone Model, Details, Flexicell">
@endsection

@section('title', 'Phone Model Details')

@section('style')
    <style>
        .product-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 12px 30px rgba(0,0,0,.08);
            overflow: hidden;
        }

        .product-header {
            background: #fff;
            color: #111;
            padding: 30px 40px 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .product-title {
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 4px;
            color: #111;
            text-shadow: none;
        }

        .product-subtitle {
            font-size: 14px;
            color: #777;
        }

        .back-button {
            background: #222;
            color: #fff;
            border-radius: 6px;
            padding: 10px 18px;
            font-size: 14px;
        }

        .back-button:hover {
            background: #000;
            color: #fff;
        }

        .image-gallery {
            background: #fafafa;
            padding: 30px;
        }

        .mySwiper2 {
            height: 420px;
            border-radius: 10px;
            background: #fff;
        }

        .mySwiper {
            height: 90px;
        }

        .swiper-slide img {
            object-fit: contain;
        }

        .product-info {
            padding: 40px;
        }

        .info-section h3 {
            font-size: 20px;
            font-weight: 600;
            margin-top: 25px;
            color: #111;
            border-bottom: none;
            margin-bottom: 15px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .info-item {
            background: #f8f8f8;
            border-radius: 8px;
            padding: 14px;
            border-left: none;
        }

        .info-label {
            font-size: 16px;
            color: #888;
        }

        .info-value {
            font-size: 18px;
            font-weight: 500;
            color: #111;
            margin-top: 5px;
        }

        .brand {
            font-size: 16px;
        }
        .release-year {
            font-size: 16px;
        }

        .badge.bg-secondary {
            background: #eee !important;
            color: #111 !important;
        }

        .specifications-table {
            box-shadow: none;
            border: 1px solid #eee;
            border-radius: 10px;
        }

        .specifications-table td {
            font-size: 14px;
        }

        .description-box {
            border-left: 3px solid #111;
            background: #fafafa;
        }

        .swiper-button-next,
        .swiper-button-prev {
            background: #fff;
            color: #111;
            box-shadow: 0 5px 15px rgba(0,0,0,.1);
        }

        @media(max-width:768px){
            .product-header {
                padding: 20px;
            }
            .product-info {
                padding: 20px;
            }
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
</style>

@endsection

@section('content')
    <div class=" text-end mt-3 mb-4">
        <a href="{{ route('admin.phone_model.index') }}" class="back-button">Back</a>
    </div>
    <div class="product-details-container">
        <div class="container-fluid">
            <div class="product-card">
                <div class="product-header">
                    <h1 class="product-title">{{ $phoneModel->model_name }}</h1>
                    <p class="product-subtitle">Technical Specifications</p>
                </div>

                <div class="row">
                    <div class="col-lg-5">
                        <div class="image-gallery">
                            @if (is_array($phoneModel->image) && count($phoneModel->image) > 0)
                                <!-- Swiper -->
                                <div style="--swiper-navigation-color: #667eea; --swiper-pagination-color: #667eea"
                                    class="swiper mySwiper2">
                                    <div class="swiper-wrapper">
                                        @foreach ($phoneModel->image as $img)
                                            <div class="swiper-slide">
                                                @php
                                                    $src = '';
                                                    if (is_string($img) && str_starts_with($img, 'data:image/')) {
                                                        $src = $img; // base64
                                                    } elseif (is_string($img) && !empty($img)) {
                                                        $src = asset('storage/phone_model/' . $img);
                                                    }
                                                @endphp

                                                @if($src)
                                                    <img src="{{ $src }}" class="d-block w-100" alt="{{ $phoneModel->model_name }}">
                                                @else
                                                    <div class="p-5 text-muted">Invalid image</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                @if (count($phoneModel->image) > 1)
                                    <div thumbsSlider="" class="swiper mySwiper">
                                        <div class="swiper-wrapper">
                                            @foreach ($phoneModel->image as $img)
                                                <div class="swiper-slide">
                                                    @php
                                                        $src = '';
                                                        if (is_string($img) && str_starts_with($img, 'data:image/')) {
                                                            $src = $img;
                                                        } elseif (is_string($img) && !empty($img)) {
                                                            $src = asset('storage/phone_model/' . $img);
                                                        }
                                                    @endphp

                                                    @if($src)
                                                        <img src="{{ $src }}" class="d-block w-100" alt="{{ $phoneModel->model_name }}">
                                                    @else
                                                        <div class="p-3 text-muted">Invalid image</div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="main-image text-center p-5" id="mainImage">
                                    <i class="fas fa-mobile-alt fa-5x text-muted mb-3"></i>
                                    <p class="text-muted">No Image Available</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="product-info">
                            <div class="info-section">
                                <h3><i class="fas fa-info-circle"></i> Basic Information</h3>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <div class="info-label">Brand</div>
                                        <div class="info-value">
                                            <span class="brand">{{ $phoneModel->brand->brand_name ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Release Year</div>
                                        <div class="info-value">
                                            <span class="release-year">{{ $phoneModel->release_year ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="info-section">
                                <h3><i class="fas fa-microchip"></i> Hardware Details</h3>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <div class="info-label">Processor</div>
                                        <div class="info-value">{{ $phoneModel->processor ?? 'N/A' }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Battery Capacity</div>
                                        <div class="info-value">{{ $phoneModel->battery_capacity ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>

                            @if ($phoneModel->description && is_array($phoneModel->description))
                                <div class="info-section">
                                    <h3><i class="fas fa-align-left"></i> Description</h3>
                                    <div class="specifications-table">
                                        <table class="table table-hover mb-0">
                                            <tbody>
                                                @foreach ($phoneModel->description as $desc)
                                                    @if(!empty($desc['key']) || !empty($desc['value']))
                                                        <tr>
                                                            <td style="width: 40%; font-weight: 600; color: #667eea;">
                                                                {{ $desc['key'] ?? '' }}
                                                            </td>
                                                            <td style="width: 60%;">{{ $desc['value'] ?? '' }}</td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @elseif($phoneModel->description)
                                <div class="info-section">
                                    <h3><i class="fas fa-align-left"></i> Description</h3>
                                    <div class="description-box">
                                        {{ $phoneModel->description }}
                                    </div>
                                </div>
                            @endif
                        </div>
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
            thumbs: {
                swiper: swiper,
            },
        });
        document.addEventListener('DOMContentLoaded', function() {
            const mainImage = document.getElementById('mainImage');
            if (mainImage) {
                mainImage.style.opacity = '0';
                setTimeout(() => {
                    mainImage.style.transition = 'opacity 0.5s ease';
                    mainImage.style.opacity = '1';
                }, 100);
            }
        });
    </script>
@endsection