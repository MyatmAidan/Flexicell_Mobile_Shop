@extends('layouts.app')

@section('meta')
    <meta name="description" content="Edit Blog">
@endsection

@section('title', 'Edit Blog')

@section('style')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.css" rel="stylesheet">
<style>
    .section-block { border: 1px solid #dee2e6; border-radius: 6px; padding: 16px; margin-bottom: 16px; background: #fdfdfd; }
    .existing-img-wrap { position: relative; display: inline-block; margin: 4px; }
    .existing-img-wrap img { width: 80px; height: 80px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; }
    .existing-img-wrap .remove-img-btn { position: absolute; top: -6px; right: -6px; background: #dc3545; border: none; border-radius: 50%; width: 20px; height: 20px; color: #fff; font-size: 10px; line-height: 20px; text-align: center; cursor: pointer; padding: 0; }
    .section-images-preview .image-preview-item { display: inline-block; margin: 4px; }
    .section-images-preview .image-preview-item img { width: 80px; height: 80px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; }
    #mainImagePreview img { width: 120px; height: 120px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd; margin-top: 8px; }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-center mb-4">
    <div class="col-md-10">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="card-title h4 d-flex align-items-center gap-2 mb-0">
                        Edit Blog
                    </h1>
                    <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="blogEditForm" action="{{ route('admin.blogs.update', $blog) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Basic Info --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="title" class="form-label">Blog Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control"
                                    placeholder="Enter blog title" required value="{{ old('title', $blog->title) }}">
                                @error('title') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Thumbnail Image</label>
                                <input type="file" name="main_image" id="main_image" class="form-control" accept="image/*">
                                <div id="mainImagePreview">
                                    @if ($blog->thumbnail)
                                        <img src="{{ asset('storage/blogs/' . $blog->thumbnail) }}" alt="Current Thumbnail">
                                    @endif
                                </div>
                                <input type="hidden" name="main_image_data" id="main_image_data">
                                @error('main_image_data') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Section Cards --}}
                    <div id="section-container">
                        @php
                            $oldTitles    = old('section_titles');
                            $oldContents  = old('section_contents');
                            $oldSectionIds = old('section_ids', []);
                        @endphp

                        @if (is_array($oldTitles) && count($oldTitles) > 0)
                            @foreach ($oldTitles as $i => $oldTitle)
                                <div class="section-block" data-section-id="{{ $oldSectionIds[$i] ?? '' }}">
                                    <div class="text-end mb-2">
                                        <button type="button" class="btn btn-sm btn-danger remove-section">
                                            <i class="fas fa-times"></i> Remove Section
                                        </button>
                                    </div>
                                    <input type="hidden" name="section_ids[]" value="{{ $oldSectionIds[$i] ?? '' }}">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Section Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="section_titles[]"
                                                placeholder="Enter section title" required value="{{ $oldTitle }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Section Images</label>
                                            <input type="file" id="section_images_{{ $i }}" name="section_images[{{ $i }}][]"
                                                class="form-control" accept="image/*" multiple>
                                            <div class="section-images-preview mt-2" data-target="section_images_{{ $i }}"></div>
                                            <div class="section-images-data" data-target="section_images_{{ $i }}"></div>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <label class="form-label">Content <span class="text-danger">*</span></label>
                                            <textarea name="section_contents[]" id="section_contents_{{ $i }}"
                                                class="form-control summernote" required>{{ $oldContents[$i] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            @foreach ($contents as $i => $content)
                                <div class="section-block" data-section-id="{{ $content->id }}">
                                    <div class="text-end mb-2">
                                        <button type="button" class="btn btn-sm btn-danger remove-section">
                                            <i class="fas fa-times"></i> Remove Section
                                        </button>
                                    </div>
                                    <input type="hidden" name="section_ids[]" value="{{ $content->id }}">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Section Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="section_titles[]"
                                                placeholder="Enter section title" required value="{{ $content->heading }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Section Images</label>
                                            <input type="file" id="section_images_{{ $i }}" name="section_images[{{ $i }}][]"
                                                class="form-control" accept="image/*" multiple>

                                            @if ($content->images->count())
                                                <div class="mt-2 existing-images-wrap" data-content-id="{{ $content->id }}">
                                                    @foreach ($content->images as $img)
                                                        <div class="existing-img-wrap">
                                                            <img src="{{ asset('storage/' . $img->image_path) }}" alt="Section image">
                                                            <button type="button" class="remove-img-btn"
                                                                data-image-id="{{ $img->id }}"
                                                                data-content-id="{{ $content->id }}">×</button>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="removed-images-inputs" data-content-id="{{ $content->id }}"></div>
                                            @endif

                                            <div class="section-images-preview mt-2" data-target="section_images_{{ $i }}"></div>
                                            <div class="section-images-data" data-target="section_images_{{ $i }}"></div>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <label class="form-label">Content <span class="text-danger">*</span></label>
                                            <textarea name="section_contents[]" id="section_contents_{{ $i }}"
                                                class="form-control summernote" required>{{ $content->content }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="removed-sections-inputs"></div>

                    <div class="d-flex justify-content-between align-items-center mt-2 mb-3">
                        <button type="button" id="add-section-btn" class="btn btn-outline-primary">
                            <i class="fas fa-plus"></i> Add Section
                        </button>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Blog
                            </button>
                            <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Thumbnail Cropper Modal --}}
<div class="modal fade" id="cropperModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crop Thumbnail</h5>
                <button type="button" class="btn-close" id="cropCancel"></button>
            </div>
            <div class="modal-body text-center">
                <div id="cropViewport" style="width:360px;height:360px;margin:0 auto;overflow:hidden;position:relative;background:#f8f9fa;border:1px solid #dee2e6;border-radius:4px;">
                    <img id="cropperImage" src="" style="position:absolute;top:0;left:0;transform-origin:top left;user-select:none;-webkit-user-drag:none;">
                </div>
                <input type="range" id="cropZoom" min="1" max="3" step="0.01" class="form-range mt-3">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cropCancelBtn">Cancel</button>
                <button type="button" class="btn btn-primary" id="cropApply">Apply Crop</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.js"></script>
<script>
$(document).ready(function () {
    let sectionIndex = {{ is_array(old('section_titles')) ? count(old('section_titles')) : count($contents) }};
    let cropModal    = new bootstrap.Modal(document.getElementById('cropperModal'), {});

    function initSummernote(selector) {
        $(selector).each(function () {
            if (!$(this).next('.note-editor').length) {
                $(this).summernote({
                    height: 200,
                    toolbar: [
                        ['style',  ['style']],
                        ['font',   ['bold', 'italic', 'underline', 'clear']],
                        ['color',  ['color']],
                        ['para',   ['ul', 'ol', 'paragraph']],
                        ['insert', ['link', 'picture']],
                        ['view',   ['codeview', 'help']],
                    ],
                    placeholder: 'Write section content here...',
                });
            }
        });
    }
    initSummernote('.summernote');

    // ── Add Section ──
    $('#add-section-btn').on('click', function () {
        let idx = sectionIndex;
        let html = `
        <div class="section-block" data-section-id="">
            <div class="text-end mb-2">
                <button type="button" class="btn btn-sm btn-danger remove-section">
                    <i class="fas fa-times"></i> Remove Section
                </button>
            </div>
            <input type="hidden" name="section_ids[]" value="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Section Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="section_titles[]" placeholder="Enter section title" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Section Images</label>
                    <input type="file" id="section_images_${idx}" name="section_images[${idx}][]" class="form-control" accept="image/*" multiple>
                    <div class="section-images-preview mt-2" data-target="section_images_${idx}"></div>
                    <div class="section-images-data" data-target="section_images_${idx}"></div>
                </div>
                <div class="col-12 mb-2">
                    <label class="form-label">Content <span class="text-danger">*</span></label>
                    <textarea name="section_contents[]" id="section_contents_${idx}" class="form-control summernote" required></textarea>
                </div>
            </div>
        </div>`;
        $('#section-container').append(html);
        initSummernote('#section_contents_' + idx);
        sectionIndex++;
    });

    // ── Remove Section ──
    $(document).on('click', '.remove-section', function () {
        let $block = $(this).closest('.section-block');
        let sectionId = $block.data('section-id');
        if (sectionId) {
            $('.removed-sections-inputs').append(`<input type="hidden" name="removed_sections[]" value="${sectionId}">`);
        }
        $block.remove();
    });

    // ── Remove Existing Image ──
    $(document).on('click', '.remove-img-btn', function () {
        let imageId   = $(this).data('image-id');
        let contentId = $(this).data('content-id');
        $(this).closest('.existing-img-wrap').remove();
        $(`.removed-images-inputs[data-content-id="${contentId}"]`).append(
            `<input type="hidden" name="removed_images[${contentId}][]" value="${imageId}">`
        );
    });

    // ── Section Image Preview ──
    $(document).on('change', 'input[id^=section_images_]', function () {
        let input   = this;
        let target  = $(input).attr('id');
        let preview = $('.section-images-preview[data-target="' + target + '"]');
        let hidden  = $('.section-images-data[data-target="' + target + '"]');
        let files   = Array.from(input.files || []).slice(0, 6);
        preview.empty(); hidden.empty();
        files.forEach(function (file) {
            let reader = new FileReader();
            reader.onload = function () {
                preview.append(`<div class="image-preview-item"><img src="${reader.result}"></div>`);
                let idx = target.replace('section_images_', '');
                hidden.append(`<input type="hidden" name="section_images_data[${idx}][]" value="${reader.result}">`);
            };
            reader.readAsDataURL(file);
        });
    });

    // ── Thumbnail Cropper ──
    $('#main_image').on('change', function () {
        let file = this.files?.[0];
        if (file && window.__openMainCropper) window.__openMainCropper(file);
    });

    (function () {
        let viewport = document.getElementById('cropViewport');
        let imgEl    = document.getElementById('cropperImage');
        let zoomEl   = document.getElementById('cropZoom');
        let st       = { scale:1, minScale:1, x:0, y:0, w:0, h:0, dragging:false, sx:0, sy:0 };

        function applyTransform() {
            imgEl.style.transform = `translate(${st.x}px,${st.y}px) scale(${st.scale})`;
        }
        function openCrop(file) {
            let reader = new FileReader();
            reader.onload = function () {
                imgEl.onload = function () {
                    st.w = imgEl.naturalWidth; st.h = imgEl.naturalHeight;
                    st.minScale = Math.max(360/st.w, 360/st.h);
                    st.scale = st.minScale;
                    zoomEl.min = st.minScale; zoomEl.max = st.minScale * 3; zoomEl.value = st.scale;
                    st.x = (360 - st.w * st.scale) / 2; st.y = (360 - st.h * st.scale) / 2;
                    applyTransform(); cropModal.show();
                };
                imgEl.src = reader.result;
            };
            reader.readAsDataURL(file);
        }
        zoomEl.addEventListener('input', function () {
            let cx = 180 - st.x, cy = 180 - st.y, prev = st.scale;
            st.scale = parseFloat(zoomEl.value);
            let r = st.scale / prev; st.x = 180 - cx * r; st.y = 180 - cy * r;
            applyTransform();
        });
        viewport.addEventListener('mousedown', e => { st.dragging = true; st.sx = e.clientX - st.x; st.sy = e.clientY - st.y; });
        window.addEventListener('mouseup',  () => { st.dragging = false; });
        window.addEventListener('mousemove', e => { if (!st.dragging) return; st.x = e.clientX - st.sx; st.y = e.clientY - st.sy; applyTransform(); });
        document.getElementById('cropCancelBtn').addEventListener('click', () => cropModal.hide());
        document.getElementById('cropCancel').addEventListener('click', () => cropModal.hide());
        document.getElementById('cropApply').addEventListener('click', function () {
            let canvas = document.createElement('canvas');
            canvas.width = 360; canvas.height = 360;
            let ctx = canvas.getContext('2d');
            ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, 360, 360);
            let sx = (-st.x)/st.scale, sy = (-st.y)/st.scale, sw = 360/st.scale, sh = 360/st.scale;
            if (sx < 0) { sw += sx; sx = 0; } if (sy < 0) { sh += sy; sy = 0; }
            if (sx + sw > st.w) sw = st.w - sx; if (sy + sh > st.h) sh = st.h - sy;
            if (sw > 0 && sh > 0) ctx.drawImage(imgEl, sx, sy, sw, sh, 0, 0, 360, 360);
            let dataUrl = canvas.toDataURL('image/jpeg', 0.92);
            canvas.toBlob(function (blob) {
                let file = document.getElementById('main_image').files?.[0];
                if (!file) return;
                let f = new File([blob], file.name.replace(/\.[^.]+$/, '') + '.jpg', { type: 'image/jpeg' });
                let dt = new DataTransfer(); dt.items.add(f);
                document.getElementById('main_image').files = dt.files;
                $('#mainImagePreview').html(`<img src="${URL.createObjectURL(blob)}">`);
                document.getElementById('main_image_data').value = dataUrl;
                cropModal.hide();
            }, 'image/jpeg', 0.92);
        });
        window.__openMainCropper = openCrop;
    })();
});
</script>
@endsection
