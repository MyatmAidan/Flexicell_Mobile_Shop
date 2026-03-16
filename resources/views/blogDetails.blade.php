@extends('layouts.user')
@section('title', $blog->title)

@section('style')
<style>
    .blog-hero-img {
        width: 100%;
        max-height: 380px;
        object-fit: cover;
        border-radius: 6px;
        margin-bottom: 28px;
    }
    .blog-title {
        font-size: 26px;
        font-weight: 700;
        color: #333;
        margin-bottom: 8px;
    }
    .blog-meta {
        font-size: 13px;
        color: #999;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid #eee;
    }
    .blog-section { margin-bottom: 40px; }
    .blog-section-heading {
        font-size: 20px;
        font-weight: 600;
        color: #333;
        margin-bottom: 14px;
        padding-bottom: 8px;
        border-bottom: 2px solid #df2020;
        display: inline-block;
    }
    .blog-content { color: #555; line-height: 1.8; }
    .blog-content img { max-width: 100%; border-radius: 4px; }
    .blog-section-images { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 18px; }
    .blog-section-images img {
        width: 160px;
        height: 120px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #e4e4e4;
        cursor: pointer;
        transition: transform .2s;
    }
    .blog-section-images img:hover { transform: scale(1.04); }
    .page-title-bar {
        background: #f8f8f8;
        border-bottom: 1px solid #e4e4e4;
        padding: 20px 0;
        margin-bottom: 30px;
    }
    /* Lightbox */
    #blogLightbox {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.85);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    #blogLightbox.active { display: flex; }
    #blogLightbox img { max-width: 90vw; max-height: 88vh; border-radius: 6px; }
    #blogLightbox .lb-close {
        position: absolute; top: 16px; right: 22px;
        font-size: 32px; color: #fff; cursor: pointer;
        background: none; border: none; line-height: 1;
    }
</style>
@endsection

@section('content')
    <!-- PAGE TITLE -->
    <div class="page-title-bar">
        <div class="container">
            <a href="{{ route('blogs.index') }}" style="color: #df2020; font-size: 13px;">
                <i class="fa fa-arrow-left"></i> Back to Blog
            </a>
        </div>
    </div>

    <div class="section">
        <div class="container" style="min-height: 60vh;">
            <div class="row">
                <div class="col-md-10 col-md-offset-1">

                    <!-- Blog Header -->
                    @if ($blog->thumbnail)
                        <img src="{{ asset('storage/blogs/' . $blog->thumbnail) }}"
                            alt="{{ $blog->title }}" class="blog-hero-img">
                    @endif

                    <h1 class="blog-title">{{ $blog->title }}</h1>
                    <div class="blog-meta">
                        <i class="fa fa-calendar-o"></i>
                        {{ $blog->created_at->format('F d, Y') }}
                        &nbsp;&middot;&nbsp;
                        {{ $contents->count() }} {{ Str::plural('section', $contents->count()) }}
                    </div>

                    <!-- Blog Sections -->
                    @foreach ($contents as $content)
                        <div class="blog-section">
                            <span class="blog-section-heading">{{ $content->heading }}</span>
                            <div class="blog-content">
                                {!! $content->content !!}
                            </div>
                            @if ($content->images->count())
                                <div class="blog-section-images">
                                    @foreach ($content->images as $img)
                                        <img src="{{ asset('storage/' . $img->image_path) }}"
                                            alt="Section image"
                                            onclick="openLightbox('{{ asset('storage/' . $img->image_path) }}')">
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <!-- Back link -->
                    <div class="mt-4 mb-4">
                        <a href="{{ route('blogs.index') }}" class="btn btn-primary">
                            <i class="fa fa-arrow-left"></i> Back to Blog List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lightbox -->
    <div id="blogLightbox" onclick="closeLightbox()">
        <button class="lb-close" onclick="closeLightbox()">&times;</button>
        <img id="lightboxImg" src="" alt="Full image">
    </div>
@endsection

@section('script')
<script>
    function openLightbox(src) {
        document.getElementById('lightboxImg').src = src;
        document.getElementById('blogLightbox').classList.add('active');
    }
    function closeLightbox() {
        document.getElementById('blogLightbox').classList.remove('active');
        document.getElementById('lightboxImg').src = '';
    }
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeLightbox();
    });
</script>
@endsection
