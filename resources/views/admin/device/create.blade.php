@extends('layouts.app')

@section('meta')
    <meta name="description" content="Create Device">
    <meta name="keywords" content="Device, Create, Flexicell">
@endsection

@section('title', 'Create Device')
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
                        Create New Device
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

                    <form id="create-device-form" action="{{ route('admin.device.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="product_id" class="form-label">Product <span class="text-danger fs-5">*</span></label>
                                    <select class="form-control" id="product_id" name="product_id" required>
                                        <option value="">Select Product</option>
                                        @foreach ($products as $p)
                                            <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>
                                                {{ $p->phoneModel->model_name ?? '-' }} ({{ $p->product_type }}) - {{ $p->phoneModel->brand->brand_name ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="imei" class="form-label">IMEI <span class="text-danger fs-5">*</span></label>
                                    <input type="text" class="form-control" id="imei" name="imei"
                                        placeholder="Enter IMEI number" value="{{ old('imei') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="ram_option_id" class="form-label">RAM <span class="text-danger fs-5">*</span></label>
                                    <select class="form-control" id="ram_option_id" name="ram_option_id" required>
                                        <option value="">Select RAM</option>
                                        @foreach(($ramOptions ?? []) as $opt)
                                            <option value="{{ $opt->id }}" @selected(old('ram_option_id') == $opt->id)>{{ $opt->value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="storage_option_id" class="form-label">Storage <span class="text-danger fs-5">*</span></label>
                                    <select class="form-control" id="storage_option_id" name="storage_option_id" required>
                                        <option value="">Select Storage</option>
                                        @foreach(($storageOptions ?? []) as $opt)
                                            <option value="{{ $opt->id }}" @selected(old('storage_option_id') == $opt->id)>{{ $opt->value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Color <span class="text-danger fs-5">*</span></label>
                                    <div id="color-selection-wrapper">
                                        <div class="d-flex gap-2 mb-1" id="existing-color-group">
                                            <select class="form-control" id="color_option_id" name="color_option_id">
                                                <option value="">Select Color</option>
                                                @foreach(($colorOptions ?? []) as $opt)
                                                    <option value="{{ $opt->id }}" @selected(old('color_option_id') == $opt->id)>{{ $opt->name }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button" class="btn btn-outline-primary btn-sm whitespace-nowrap" id="btn-toggle-new-color" title="Add New Color">
                                                <i class="fa fa-plus"></i> New
                                            </button>
                                        </div>
                                        <div class="d-none" id="new-color-group">
                                            <div class="d-flex gap-2">
                                                <input type="text" class="form-control" id="new_color_name" name="new_color_name" placeholder="Color Name (e.g. Silver)">
                                                <input type="color" class="form-control form-control-color" id="new_color_value" name="new_color_value" value="#000000" title="Choose a color" style="width: 60px; height: 38px;">
                                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-cancel-new-color" title="Cancel New Color">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </div>
                                            <small class="text-muted">Defining a new color that will be saved on submit.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="battery_percentage" class="form-label">Battery % <span class="text-danger fs-5">*</span></label>
                                    <input type="number" class="form-control" id="battery_percentage" name="battery_percentage"
                                        placeholder="0-100" value="{{ old('battery_percentage') }}" min="0" max="100" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="condition_grade" class="form-label">Condition Grade <span class="text-danger fs-5">*</span></label>
                                    <input type="text" class="form-control" id="condition_grade" name="condition_grade"
                                        placeholder="e.g. A, B, C" value="{{ old('condition_grade') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>Available</option>
                                        <option value="sold" {{ old('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                                        <option value="reserved" {{ old('status') == 'reserved' ? 'selected' : '' }}>Reserved</option>
                                        <option value="defective" {{ old('status') == 'defective' ? 'selected' : '' }}>Defective</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="purchase_price" class="form-label">Purchase Price</label>
                                    <input type="number" step="0.01" class="form-control" id="purchase_price" name="purchase_price"
                                        placeholder="0.00" value="{{ old('purchase_price') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="selling_price" class="form-label">Selling Price</label>
                                    <input type="number" step="0.01" class="form-control" id="selling_price" name="selling_price"
                                        placeholder="0.00" value="{{ old('selling_price') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="warranty_id" class="form-label">Warranty</label>
                                    <select class="form-control" id="warranty_id" name="warranty_id">
                                        <option value="">No Warranty</option>
                                        @foreach(($warranties ?? []) as $w)
                                            <option value="{{ $w->id }}" @selected(old('warranty_id') == $w->id)>
                                                {{ $w->warranty_month }} Months ({{ ucfirst($w->status) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="image-input" class="form-label">Images</label>
                            <input type="file" class="form-control" id="image-input" accept="image/*" multiple>
                            <div class="mt-2" id="image-preview-wrapper"></div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-1 mb-3 me-3">
                            <a href="{{ route('admin.device.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Create Device
                            </button>
                        </div>
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
                    // For create, we append to DOM wrapper
                    $('#image-preview-wrapper').append(`
                        <div class="image-preview-item">
                            <img src="${base64data}" width="100">
                            <input type="hidden" name="image[]" value="${base64data}">
                            <button type="button" class="btn btn-danger btn-sm remove-image-btn"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                    `);
                    cropperModal.hide();
                    
                    // Reset file input so same file can trigger change again
                    $('#image-input').val('');
                };
            }, 'image/jpeg');
        });

        $('#image-preview-wrapper').on('click', '.remove-image-btn', function() {
            $(this).closest('.image-preview-item').remove();
        });


        $('#create-device-form').on('submit', function(e) {
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
                        window.location.href = "{{ route('admin.device.index') }}";
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

        // Toggle Color Logic
        $('#btn-toggle-new-color').on('click', function() {
            $('#existing-color-group').addClass('d-none');
            $('#new-color-group').removeClass('d-none');
            $('#color_option_id').val('').prop('required', false);
            $('#new_color_name').prop('required', true).focus();
        });

        $('#btn-cancel-new-color').on('click', function() {
            $('#new-color-group').addClass('d-none');
            $('#existing-color-group').removeClass('d-none');
            $('#new_color_name').val('').prop('required', false);
            $('#color_option_id').prop('required', true);
        });
    });
</script>
@endsection
