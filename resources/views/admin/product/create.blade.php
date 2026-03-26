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
    .image-preview-item .remove-image-btn {
        position: absolute; top: -5px; right: -5px;
        border-radius: 50%; width: 20px; height: 20px;
        padding: 0; font-size: 10px;
    }
    .form-switch-lg .form-check-input {
        width: 60px;
        height: 32px;
        cursor: pointer;
    }

    .form-switch-lg .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .form-switch-lg .form-check-input::before {
        width: 28px;
        height: 28px;
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-center mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">

                <h1 class="card-title h4 mb-4">Add New Product</h1>

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
                        {{-- SWITCH --}}
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="d-block">Product Type <span class="text-danger">*</span></label>

                                <div class="d-flex align-items-center gap-3 mt-2">
                                    <span class="text-muted fw-semibold">New</span>

                                    <div class="form-check form-switch form-switch-lg m-0">
                                        <input class="form-check-input" type="checkbox" id="product_type_switch">
                                    </div>

                                    <span class="text-muted fw-semibold">Second Hand</span>
                                </div>

                                <input type="hidden" name="product_type" id="product_type" value="new">
                            </div>
                        </div>

                        {{-- Phone Model --}}
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Phone Model <span class="text-danger">*</span></label>
                                <select class="form-control" name="phone_model_id" required>
                                    <option value="">Select Phone Model</option>
                                    @foreach ($phoneModels as $pm)
                                        <option value="{{ $pm->id }}">
                                            {{ $pm->model_name }} ({{ $pm->brand->brand_name ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Selling Price <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="selling_price" class="form-control" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Warranty (months)</label>
                                <input type="number" name="warranty_month" class="form-control" placeholder="Optional">
                            </div>
                        </div>
                    </div>

                    {{-- NEW DEVICE --}}
                    <div id="new-device-section">
                        <h5 class="mt-3">New Device </h5>

                        <div class="mt-3 form-group">
                            <label>Stock Quantity</label>
                            <input type="number" name="stock_quantity" class="form-control" value="0">
                            <small class="text-muted">Increase to add more placeholder devices. Edit each device to add IMEI and details.</small>
                        </div>
                    </div>

                    {{-- SECOND HAND --}}
                    <div id="second-hand-section" style="display:none;">
                        <h5 class="mt-3">Second Hand Device </h5>

                        <div class="row">
                            <div class="col-md-6">
                                <label>IMEI *</label>
                                <input type="text" name="imei" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label>RAM *</label>
                                <select name="ram_option_id" class="form-control">
                                    <option value="">Select RAM</option>
                                    @foreach(($ramOptions ?? []) as $opt)
                                        <option value="{{ $opt->id }}">{{ $opt->value }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mt-3">
                                <label>Storage *</label>
                                <select name="storage_option_id" class="form-control">
                                    <option value="">Select Storage</option>
                                    @foreach(($storageOptions ?? []) as $opt)
                                        <option value="{{ $opt->id }}">{{ $opt->value }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mt-3">
                                <label class="form-label">Color <span class="text-danger">*</span></label>
                                <div id="color-selection-wrapper">
                                    <div class="d-flex gap-2 mb-1" id="existing-color-group">
                                        <select name="color_option_id" id="color_option_id" class="form-control">
                                            <option value="">Select Color</option>
                                            @foreach(($colorOptions ?? []) as $opt)
                                                <option value="{{ $opt->id }}">{{ $opt->name }}</option>
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
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <label>Condition Grade *</label>
                                <select name="condition_grade" class="form-control">
                                    <option value="A">A (Like New)</option>
                                    <option value="B">B (Good)</option>
                                    <option value="C">C (Used)</option>
                                </select>
                            </div>

                            <div class="col-md-6 mt-3">
                                <label>Battery (%) *</label>
                                <input type="number" name="battery_percentage" class="form-control">
                            </div>

                            <div class="col-md-6 mt-3">
                                <label>Buy Price *</label>
                                <input type="number" step="0.01" name="buy_price" class="form-control">
                            </div>

                            <div class="col-md-6 mt-3">
                                <label>Purchase Date *</label>
                                <input type="datetime-local" name="purchase_at" class="form-control">
                            </div>
                        </div>
                    </div>

                    {{-- DESCRIPTION --}}
                    <div class="mt-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>

                    {{-- IMAGE --}}
                    <div class="mt-3">
                        <label>Images</label>
                        <input type="file" id="image-input" class="form-control" multiple>
                        <div id="image-preview-wrapper" class="mt-2"></div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-1 mb-3 me-3">
                        <a href="{{ route('admin.product.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Create Product
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

{{-- CROPPER MODAL --}}
<div class="modal fade" id="cropper-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Crop Image</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <img id="cropper-image" style="max-width:100%;">
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="crop-button">Crop</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(function () {

    // SWITCH
    $('#product_type_switch').on('change', function () {
        if ($(this).is(':checked')) {
            $('#product_type').val('second hand');
            $('#switch-label').text('Second Hand Device');
            $('#new-device-section').hide();
            $('#second-hand-section').show();
        } else {
            $('#product_type').val('new');
            $('#switch-label').text('New Device');
            $('#new-device-section').show();
            $('#second-hand-section').hide();
        }
    });

    // IMAGE CROPPER
    let cropper;
    let modal = new bootstrap.Modal(document.getElementById('cropper-modal'));
    let image = document.getElementById('cropper-image');

    $('#image-input').on('change', function(e) {
        let file = e.target.files[0];
        let reader = new FileReader();

        reader.onload = function(e) {
            image.src = e.target.result;
            modal.show();
        };

        reader.readAsDataURL(file);
    });

    $('#cropper-modal').on('shown.bs.modal', function() {
        cropper = new Cropper(image, { aspectRatio: 1 });
    }).on('hidden.bs.modal', function() {
        cropper.destroy();
    });

    $('#crop-button').click(function() {
        let canvas = cropper.getCroppedCanvas({ width: 600, height: 600 });

        canvas.toBlob(function(blob) {
            let reader = new FileReader();
            reader.readAsDataURL(blob);

            reader.onloadend = function () {
                let base64 = reader.result;

                $('#image-preview-wrapper').append(`
                    <div class="image-preview-item">
                        <img src="${base64}" width="100">
                        <input type="hidden" name="image[]" value="${base64}">
                        <button type="button" class="btn btn-danger btn-sm remove-image-btn">x</button>
                    </div>
                `);

                modal.hide();
                $('#image-input').val('');
            };
        });
    });

    $(document).on('click', '.remove-image-btn', function () {
        $(this).parent().remove();
    });


    // AJAX SUBMIT
    $('#create-product-form').submit(function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                Swal.fire('Success', res.message, 'success')
                    .then(() => window.location.href = "{{ route('admin.product.index') }}");
            },
            error: function(xhr) {
                let msg = 'Error';
                if (xhr.responseJSON?.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }
                Swal.fire('Error', msg, 'error');
            }
        });
    });

        // JS logic to toggle between existing color selection and new color creation
        $('#btn-toggle-new-color').on('click', function() {
            $('#existing-color-group').addClass('d-none');
            $('#new-color-group').removeClass('d-none');
            $('#color_option_id').val('');
        });

        $('#btn-cancel-new-color').on('click', function() {
            $('#new-color-group').addClass('d-none');
            $('#existing-color-group').removeClass('d-none');
            $('#new_color_name').val('');
        });
    });
</script>
@endsection
```