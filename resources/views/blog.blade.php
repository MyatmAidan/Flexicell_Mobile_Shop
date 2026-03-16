@extends('layouts.user')
@section('title', 'Blog')

@section('style')
<style>
    .blog-card {
        border: 1px solid #e4e4e4;
        border-radius: 6px;
        overflow: hidden;
        transition: box-shadow .2s;
        background: #fff;
        height: 100%;
    }
    .blog-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.12); }
    .blog-card-img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    .blog-card-body { padding: 16px; }
    .blog-card-title {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .blog-card-meta { font-size: 12px; color: #999; margin-bottom: 10px; }
    .btn-read-more {
        background: #df2020;
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 6px 16px;
        font-size: 13px;
        text-decoration: none;
        display: inline-block;
    }
    .btn-read-more:hover { background: #c01818; color: #fff; }
    .page-title-bar {
        background: #f8f8f8;
        border-bottom: 1px solid #e4e4e4;
        padding: 20px 0;
        margin-bottom: 30px;
    }
</style>
@endsection

@section('content')
    <!-- PAGE TITLE -->
    <div class="page-title-bar">
        <div class="container">
            <h2 style="font-size: 24px; font-weight: 700; margin: 0; color: #333;">Blog</h2>
        </div>
    </div>

    <!-- BLOG LIST -->
    <div class="section">
        <div class="container" style="min-height: 60vh;">

            @if ($blogs->isEmpty())
                <div class="text-center py-5">
                    <i class="fa fa-newspaper-o fa-3x text-muted mb-3" style="display:block;"></i>
                    <p class="text-muted">No blog posts yet. Check back soon!</p>
                </div>
            @else
                <div class="row">
                    @foreach ($blogs as $blog)
                        <div class="col-md-4 col-sm-6 mb-4">
                            <div class="blog-card">
                                @if ($blog->thumbnail)
                                    <img src="{{ asset('storage/blogs/' . $blog->thumbnail) }}"
                                        alt="{{ $blog->title }}" class="blog-card-img">
                                @else
                                    <div class="blog-card-img d-flex align-items-center justify-content-center bg-light">
                                        <i class="fa fa-image fa-3x text-muted"></i>
                                    </div>
                                @endif
                                <div class="blog-card-body">
                                    <div class="blog-card-meta">
                                        <i class="fa fa-calendar-o"></i>
                                        {{ $blog->created_at->format('M d, Y') }}
                                        &nbsp;&middot;&nbsp;
                                        {{ $blog->contents_count ?? $blog->contents->count() }} sections
                                    </div>
                                    <div class="blog-card-title">{{ $blog->title }}</div>
                                    <a href="{{ route('blogs.show', $blog) }}" class="btn-read-more">
                                        Read More <i class="fa fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="text-center mt-2">
                    {{ $blogs->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
