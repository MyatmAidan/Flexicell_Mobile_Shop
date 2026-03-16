@extends('layouts.app')

@section('meta')
    <meta name="description" content="{{ $blog->title }}">
@endsection

@section('title', $blog->title)

@section('style')
<style>
    .blog-content img { max-width: 100%; border-radius: 4px; }
    .blog-content h1,.blog-content h2,.blog-content h3 { margin-top: 1rem; }
    .section-image-grid { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
    .section-image-grid img { width: 160px; height: 120px; object-fit: cover; border-radius: 4px; border: 1px solid #dee2e6; }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                {{-- Header --}}
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                    <div>
                        <h1 class="card-title h4 mb-1">{{ $blog->title }}</h1>
                        <small class="text-muted">
                            <i class="fas fa-calendar-alt me-1"></i>
                            {{ $blog->created_at?->format('M d, Y') }}
                            &nbsp;&middot;&nbsp;
                            <span class="badge bg-info text-white">{{ $contents->count() }} sections</span>
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.blogs.edit', $blog) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>

                {{-- Thumbnail --}}
                @if ($blog->thumbnail)
                    <img src="{{ asset('storage/blogs/' . $blog->thumbnail) }}"
                        alt="{{ $blog->title }}"
                        style="width:100%;max-height:320px;object-fit:cover;border-radius:6px;border:1px solid #dee2e6;">
                @endif
            </div>
        </div>

        {{-- Sections --}}
        @foreach ($contents as $i => $content)
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">
                        <span class="badge bg-secondary me-2">{{ $i + 1 }}</span>
                        {{ $content->heading }}
                    </h5>
                    <div class="blog-content">
                        {!! $content->content !!}
                    </div>
                    @if ($content->images->count())
                        <div class="section-image-grid">
                            @foreach ($content->images as $img)
                                <img src="{{ asset('storage/' . $img->image_path) }}" alt="Section image">
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
