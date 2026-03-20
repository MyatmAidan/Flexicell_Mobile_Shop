@extends('layouts.app')

@section('meta')
    <meta name="description" content="Create Product">
    <meta name="keywords" content="Product, Create, Flexicell">
@endsection

@section('title', 'Create Product')
@section('style')
<style>
    .image-preview-item { display: inline-block; margin: 5px; position: relative; }
    .image-preview-item img { border: 1px solid #ddd; border-radius: 4px; }
    .image-preview-item .remove-image-btn { position: absolute; top: -5px; right: -5px; border-radius: 50%; width: 20px; height: 20px; padding: 0; font-size: 10px; }
</style>
@endsection

@section('content')
    <div class="d-flex justify-content-center mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h1 class="card-title h4 d-flex align-items-center gap-2 mb-4">
                        Create New Product
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

                    <form id="create-product-form" action="{{ route('admin.product.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="phone_model_id" class="form-label">Phone Model <span class="text-danger fs-5">*</span></label>
                                    <select class="form-control" id="phone_model_id" name="phone_model_id" required>
                                        <option value="">Select Phone Model</option>
                                        @foreach ($phoneModels as $pm)
                                            <option value="{{ $pm->id }}" {{ old('phone_model_id') == $pm->id ? 'selected' : '' }}>
                                                {{ $pm->model_name }} ({{ $pm->brand->brand_name ?? '-' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="product_type" class="form-label">Product Type <span class="text-danger fs-5">*</span></label>
                                    <select class="form-control" id="product_type" name="product_type" required>
                                        <option value="">Select Type</option>
                                        <option value="new" {{ old('product_type') == 'new' ? 'selected' : '' }}>New</option>
                                        <option value="second hand" {{ old('product_type') == 'second hand' ? 'selected' : '' }}>Second Hand</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="selling_price" class="form-label">Selling Price <span class="text-danger fs-5">*</span></label>
                                    <input type="number" step="0.01" class="form-control" id="selling_price" name="selling_price"
                                        placeholder="0.00" value="{{ old('selling_price') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="warranty_month" class="form-label">Warranty (months)</label>
                                    <input type="number" class="form-control" id="warranty_month" name="warranty_month"
                                        placeholder="Optional" value="{{ old('warranty_month') }}" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="stock_quantity" class="form-label">Stock Quantity</label>
                                    <input type="number" class="form-control" id="stock_quantity" name="stock_quantity"
                                        placeholder="e.g. 30" value="{{ old('stock_quantity', 0) }}" min="0">
                                    <small class="text-muted">Creates one device record per unit. Edit each device later to add IMEI, RAM, storage, color, etc.</small>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Product description">{{ old('description') }}</textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="image-input" class="form-label">Images</label>
                            <input type="file" class="form-control" id="image-input" accept="image/*" multiple>
                            <div class="mt-2" id="image-preview-wrapper"></div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Create Product</button>
                        <a href="{{ route('admin.product.index') }}" class="btn btn-secondary mt-3">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Cropper Modal -->
    <div class="modal fade" id="cropper-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crop Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <img id="cropper-image" src="" alt="Crop" style="max-width: 100%;">
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
    $(document).ready(function () {
        let cropper;
        let cropperModal = new bootstrap.Modal(document.getElementById('cropper-modal'));
        const cropperImage = document.getElementById('cropper-image');
        let currentFile;

        $('#image-input').on('change', function(e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                Array.from(files).forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        cropperImage.src = e.target.result;
                        cropperModal.show();
                    };
                    reader.readAsDataURL(file);
                });
            }
        });

        if (document.getElementById('cropper-modal')) {
            $('#cropper-modal').on('shown.bs.modal', function() {
                cropper = new Cropper(cropperImage, { aspectRatio: 1, viewMode: 1 });
            }).on('hidden.bs.modal', function() {
                if (cropper) { cropper.destroy(); cropper = null; }
            });
        }

        $('#crop-button').on('click', function() {
            if (!cropper) return;
            const canvas = cropper.getCroppedCanvas({ width: 600, height: 600 });
            canvas.toBlob(function(blob) {
                const reader = new FileReader();
                reader.readAsDataURL(blob);
                reader.onloadend = function() {
                    const base64data = reader.result;
                    $('#image-preview-wrapper').append(`
                        <div class="image-preview-item">
                            <img src="${base64data}" width="100">
                            <input type="hidden" name="image[]" value="${base64data}">
                            <button type="button" class="btn btn-danger btn-sm remove-image-btn"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                    `);
                    cropperModal.hide();
                    
                    // Reset file input
                    $('#image-input').val('');
                };
            }, 'image/jpeg');
        });

        $('#image-preview-wrapper').on('click', '.remove-image-btn', function() {
            $(this).closest('.image-preview-item').remove();
        });

        $('#create-product-form').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({ icon: 'success', title: 'Success', text: response.message }).then(() => {
                        window.location.href = "{{ route('admin.product.index') }}";
                    });
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON?.errors;
                    let msg = xhr.responseJSON?.message || 'Something went wrong';
                    if (errors) {
                        msg = Object.values(errors).flat().join('<br>');
                    }
                    Swal.fire({ icon: 'error', title: 'Error', html: msg });
                }
            });
        });
    });
</script>
@endsection
