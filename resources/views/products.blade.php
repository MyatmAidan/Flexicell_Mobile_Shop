@extends('layouts.user')
@section('title', 'All Products')
@section('content')
    <!-- BREADCRUMB -->
    <div id="breadcrumb" class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <ul class="breadcrumb-tree">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li class="active">All Categories</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION -->
    <div class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-title">
                        <h3 class="title">Our Product Categories</h3>
                    </div>
                </div>
                @foreach ($categories as $category)
                    <div class="col-md-4 col-xs-6">
                        <div class="shop" style="cursor: pointer;" onclick="window.location.href='{{ route('products.search', ['category_id' => $category->id]) }}'">
                            <div class="shop-img">
                                <img src="{{ asset('img/logo.png') }}" alt="">
                            </div>
                            <div class="shop-body">
                                <h3>{{ $category->category_name }}<br>Collection</h3>
                                <a href="{{ route('products.search', ['category_id' => $category->id]) }}" class="cta-btn">Shop Now <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
