@extends('layouts.app')

@section('meta')
    <meta name="description" content="Edit Phone Model">
    <meta name="keywords" content="Phone Model, Edit, Flexicell">
@endsection

@section('title', 'Edit Phone Model')
@section('style')
    <style>
        .image-preview-item {
            display: inline-block;
            margin: 5px;
            position: relative;
        }

        .image-preview-item img {
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .image-preview-item .remove-image-btn {
            position: absolute;
            top: -5px;
            right: -5px;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            padding: 0;
            font-size: 10px;
        }
    </style>
@endsection

@section('content')
    <div class="d-flex justify-content-center mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h1 class="card-title h4 d-flex align-items-center gap-2 mb-4">
                        Edit Phone Model
                    </h1>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="">
                        <form id="edit-phone-model-form" action="{{ route('admin.phone_model.update', $phoneModel->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="name" class="form-label">Model Name <span
                                                class="text-danger fs-5">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            placeholder="Enter product name" value="{{ old('name', $phoneModel->model_name) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="brand_id" class="form-label">Brand <span
                                                class="text-danger fs-5">*</span></label>
                                        <select class="form-control" id="brand_id" name="brand_id">
                                            <option value="">Select Brand</option>
                                            @foreach ($brands as $brand)
                                                <option value="{{ $brand->id }}"
                                                    {{ old('brand_id', $phoneModel->brand_id) == $brand->id ? 'selected' : '' }}>
                                                    {{ $brand->brand_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="processor" class="form-label">Processor <span
                                                class="text-danger fs-5">*</span></label>
                                        <input type="text" class="form-control" id="processor" name="processor"
                                            placeholder="Enter processor" value="{{ old('processor', $phoneModel->processor) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="battery_capacity" class="form-label">Battery Capacity (mAh) <span
                                                class="text-danger fs-5">*</span></label>
                                        <input type="text" class="form-control" id="battery_capacity" name="battery_capacity"
                                            placeholder="Enter battery capacity" value="{{ old('battery_capacity', $phoneModel->battery_capacity) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="category_id" class="form-label">Category <span
                                                class="text-danger fs-5">*</span></label>
                                        <select class="form-control" id="category_id" name="category_id">
                                            <option value="">Select Category</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('category_id', $phoneModel->category_id) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->category_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="release_year" class="form-label">Release Year <span
                                                class="text-danger fs-5">*</span></label>
                                        <input type="number" class="form-control" id="release_year" name="release_year"
                                            placeholder="Enter release year" value="{{ old('release_year', $phoneModel->release_year) }}">
                                    </div> 
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Description</label>
                                <div id="description-wrapper">
                                    @php
                                        $description = old('description', $phoneModel->description ?? [['key' => '', 'value' => '']]);
                                    @endphp
                                    @foreach ($description as $index => $item)
                                        <div class="row mb-2">
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="description[{{ $index }}][key]"
                                                    value="{{ $item['key'] ?? '' }}" placeholder="Key">
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="description[{{ $index }}][value]"
                                                    value="{{ $item['value'] ?? '' }}" placeholder="Value">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-danger remove-description-btn"><i
                                                        class="fa-solid fa-xmark"></i></button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="add-description-btn">
                                    <i class="fa fa-plus"></i> Add Description
                                </button>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Available Colors</label>
                                <div id="color-wrapper">
                                    @php
                                        $available_colors = old('available_color', $phoneModel->available_color ?? []);
                                        if (empty($available_colors)) {
                                            $available_colors = [['name' => '', 'value' => '#000000']];
                                        }
                                    @endphp
                                    @foreach ($available_colors as $index => $color)
                                        @php
                                            $cName = is_array($color) ? ($color['name'] ?? '') : '';
                                            $cValue = is_array($color) ? ($color['value'] ?? '#000000') : $color;
                                        @endphp
                                        <div class="row mb-2">
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="available_color[{{ $index }}][name]"
                                                    value="{{ $cName }}" placeholder="Color Name">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="color" class="form-control form-control-color w-100" name="available_color[{{ $index }}][value]"
                                                    value="{{ $cValue }}" title="Choose a color" style="height: 38px;">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-danger remove-color-btn"><i
                                                        class="fa-solid fa-xmark"></i></button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="add-color-btn">
                                    <i class="fa fa-plus"></i> Add Color
                                </button>
                            </div>
                            <div class="form-group mb-3">
                                <label for="logo-input" class="form-label">Images <span
                                        class="text-danger fs-5">*</span></label>
                                <input type="file" class="form-control" id="logo-input" accept="image/*" multiple>
                                <div class="mt-2" id="image-preview-wrapper">
                                    @if ($phoneModel->image)
                                        @foreach ($phoneModel->image as $img)
                                            <div class="image-preview-item">
                                                <img src="{{ asset('storage/phone_model/' . $img) }}" width="100">
                                                <input type="hidden" name="image[]" value="{{ $img }}">
                                                <button type="button" class="btn btn-danger btn-sm remove-image-btn"><i class="fa-solid fa-xmark"></i></button>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-1 mb-3 me-3">
                                <a href="{{ route('admin.phone_model.index') }}" class="btn btn-secondary">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Update Phone Model
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cropper Modal -->
    <div class="modal fade" id="cropper-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        role="dialog" aria-labelledby="cropperModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cropperModalLabel">Crop Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <img id="cropper-image" src="" alt="Crop" style="max-width: 100%;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="crop-button">Crop</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            let specIndex = {{ count($description ?? []) }};
            $('#add-description-btn').on('click', function() {
                $('#description-wrapper').append(`
                    <div class="row mb-2">
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="description[${specIndex}][key]" placeholder="Key">
                        </div>
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="description[${specIndex}][value]" placeholder="Value">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger remove-description-btn"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                    </div>
                `);
                specIndex++;
            });

            $('#description-wrapper').on('click', '.remove-description-btn', function() {
                $(this).closest('.row').remove();
            });

            let colorIndex = {{ count($available_colors ?? []) }};
            $('#add-color-btn').on('click', function() {
                $('#color-wrapper').append(`
                    <div class="row mb-2">
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="available_color[${colorIndex}][name]" placeholder="Color Name">
                        </div>
                        <div class="col-md-3">
                            <input type="color" class="form-control form-control-color w-100" name="available_color[${colorIndex}][value]" value="#000000" title="Choose a color" style="height: 38px;">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger remove-color-btn"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                    </div>
                `);
                colorIndex++;
            });

            $('#color-wrapper').on('click', '.remove-color-btn', function() {
                $(this).closest('.row').remove();
            });

            let cropper;
            let cropperModal = new bootstrap.Modal(document.getElementById('cropper-modal'));
            const image = document.getElementById('cropper-image');
            let currentFile;

            $('#logo-input').on('change', function(e) {
                const files = e.target.files;
                if (files && files.length > 0) {
                    Array.from(files).forEach((file, index) => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            if (index === 0) {
                                currentFile = file;
                                image.src = e.target.result;
                                cropperModal.show();
                            } else {
                                const base64data = e.target.result;
                                const preview = `
                                    <div class="image-preview-item">
                                        <img src="${base64data}" width="100">
                                        <input type="hidden" name="image[]" value="${base64data}">
                                        <button type="button" class="btn btn-danger btn-sm remove-image-btn"><i class="fa-solid fa-xmark"></i></button>
                                    </div>
                                `;
                                $('#image-preview-wrapper').append(preview);
                            }
                        };
                        reader.readAsDataURL(file);
                    });
                }
            });

            $('#cropper-modal').on('shown.bs.modal', function() {
                cropper = new Cropper(image, {
                    aspectRatio: 1,
                    viewMode: 1,
                    preview: '.preview'
                });
            }).on('hidden.bs.modal', function() {
                if(cropper) {
                    cropper.destroy();
                    cropper = null;
                }
            });

            $('#crop-button').on('click', function() {
                const canvas = cropper.getCroppedCanvas({
                    width: 600,
                    height: 600,
                });

                canvas.toBlob(function(blob) {
                    const reader = new FileReader();
                    reader.readAsDataURL(blob);
                    reader.onloadend = function() {
                        const base64data = reader.result;
                        const preview = `
                            <div class="image-preview-item">
                                <img src="${base64data}" width="100">
                                <input type="hidden" name="image[]" value="${base64data}">
                                <button type="button" class="btn btn-danger btn-sm remove-image-btn"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                        `;
                        $('#image-preview-wrapper').append(preview);
                        cropperModal.hide();
                    };
                });
            });

            $('#image-preview-wrapper').on('click', '.remove-image-btn', function() {
                $(this).closest('.image-preview-item').remove();
            });

            $('#edit-phone-model-form').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: response.message || 'Saved',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true,
                        }).then(() => {
                            window.location.href = "{{ route('admin.phone_model.index') }}";
                        });
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorHtml = '<ul>';
                        $.each(errors, function(key, value) {
                            errorHtml += '<li>' + value[0] + '</li>';
                        });
                        errorHtml += '</ul>';
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: errorHtml,
                        });
                    }
                });
            });
        });
    </script>
@endsection
