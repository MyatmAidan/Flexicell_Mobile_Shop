@extends('layouts.app')

@section('meta')
    <meta name="description" content="Edit Product">
    <meta name="keywords" content="Product, Edit, Flexicell">
@endsection

@section('title', 'Edit Product')
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
                        Edit Product
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

                    <form id="edit-product-form" action="{{ route('admin.product.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="phone_model_id" class="form-label">Phone Model <span class="text-danger fs-5">*</span></label>
                                    <select class="form-control" id="phone_model_id" name="phone_model_id" required>
                                        <option value="">Select Phone Model</option>
                                        @foreach ($phoneModels as $pm)
                                            <option value="{{ $pm->id }}" {{ old('phone_model_id', $product->phone_model_id) == $pm->id ? 'selected' : '' }}>
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
                                        <option value="new" {{ old('product_type', $product->product_type) == 'new' ? 'selected' : '' }}>New</option>
                                        <option value="second hand" {{ old('product_type', $product->product_type) == 'second hand' ? 'selected' : '' }}>Second Hand</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="stock_quantity" class="form-label">Stock Quantity</label>
                                    <input type="number" class="form-control" id="stock_quantity" name="stock_quantity"
                                        placeholder="0" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0">
                                    <small class="text-muted">Increase to add more placeholder devices. Edit each device to add IMEI and details.</small>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Product description">{{ old('description', $product->description) }}</textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="image-input" class="form-label">Images</label>
                            <input type="file" class="form-control" id="image-input" accept="image/*" multiple>
                            <div class="mt-2" id="image-preview-wrapper">
                                @php $images = is_array($product->image) ? $product->image : ($product->image ? (array) $product->image : []); @endphp
                                @foreach ($images as $img)
                                    @if ($img)
                                        <div class="image-preview-item">
                                            <img src="{{ asset('storage/products/' . $img) }}" width="100" alt="Product">
                                            <input type="hidden" name="image[]" value="{{ $img }}">
                                            <button type="button" class="btn btn-danger btn-sm remove-image-btn"><i class="fa-solid fa-xmark"></i></button>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Update Product</button>
                        <a href="{{ route('admin.product.index') }}" class="btn btn-secondary mt-3">Cancel</a>
                    </form>

                    @if ($product->devices->count() > 0)
                        <hr class="my-4">
                        <h5 class="mb-3">Devices ({{ $product->devices->count() }})</h5>
                        <p class="text-muted small">Edit each device to add IMEI, RAM, storage, color, battery %, and condition details.</p>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>IMEI</th>
                                        <th>RAM</th>
                                        <th>Storage</th>
                                        <th>Color</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($product->devices as $index => $device)
                                        <tr data-device-id="{{ $device->id }}"
                                            data-imei="{{ $device->imei }}"
                                            data-ram_option_id="{{ $device->ram_option_id }}"
                                            data-storage_option_id="{{ $device->storage_option_id }}"
                                            data-color_option_id="{{ $device->color_option_id }}"
                                            data-warranty_id="{{ $device->warranty_id }}"
                                            data-battery="{{ $device->battery_percentage }}"
                                            data-condition="{{ $device->condition_grade }}"
                                            data-status="{{ $device->status }}"
                                            data-purchase_price="{{ $device->purchase_price }}"
                                            data-selling_price="{{ $device->selling_price }}"
                                            data-image="{{ is_array($device->image) ? json_encode($device->image) : ($device->image ? json_encode([$device->image]) : '[]') }}"
                                            data-product-id="{{ $device->product_id }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td class="device-imei">{{ $device->imei ?? '-' }}</td>
                                            <td class="device-ram">{{ $device->ramOption?->value ?? '-' }}</td>
                                            <td class="device-storage">{{ $device->storageOption?->value ?? '-' }}</td>
                                            <td class="device-color">{{ $device->colorOption?->value ?? '-' }}</td>
                                            <td><span class="badge bg-{{ $device->status == 'available' ? 'success' : ($device->status == 'sold' ? 'secondary' : 'warning') }} device-status">{{ $device->status }}</span></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary edit-device-btn" data-device-id="{{ $device->id }}">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('admin.device.partials.edit-modal')

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

        $('#image-input').on('change', function(e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                Array.from(files).forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if (index === 0) {
                            cropperImage.src = e.target.result;
                            cropperModal.show();
                        } else {
                            const base64data = e.target.result;
                            $('#image-preview-wrapper').append(`
                                <div class="image-preview-item">
                                    <img src="${base64data}" width="100">
                                    <input type="hidden" name="image[]" value="${base64data}">
                                    <button type="button" class="btn btn-danger btn-sm remove-image-btn"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                            `);
                        }
                    };
                    reader.readAsDataURL(file);
                });
            }
        });

        $('#cropper-modal').on('shown.bs.modal', function() {
            cropper = new Cropper(cropperImage, { aspectRatio: 1, viewMode: 1 });
        }).on('hidden.bs.modal', function() {
            if (cropper) { cropper.destroy(); cropper = null; }
        });

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
                };
            });
        });

        $('#image-preview-wrapper').on('click', '.remove-image-btn', function() {
            $(this).closest('.image-preview-item').remove();
        });

        $('#edit-product-form').on('submit', function(e) {
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

        // Device Edit Modal (product edit page - use data from row)
        const productsForModal = @json($products->map(fn($p) => ['id' => $p->id, 'label' => ($p->phoneModel->model_name ?? '-') . ' (' . $p->product_type . ') - ' . ($p->phoneModel->brand->brand_name ?? '')]));
        const deviceEditModal = new bootstrap.Modal(document.getElementById('deviceEditModal'));
        const updateDeviceUrl = "{{ url('admin/device/update') }}";

        $(document).on('click', '.edit-device-btn', function() {
            const row = $(this).closest('tr');
            const deviceId = row.data('device-id');
            $('#device_edit_id').val(deviceId);
            $('#modal_product_id').empty().append('<option value="">Select Product</option>');
            productsForModal.forEach(p => {
                $('#modal_product_id').append(`<option value="${p.id}" ${row.data('product-id') == p.id ? 'selected' : ''}>${p.label}</option>`);
            });
            $('#modal_imei').val(row.data('imei'));
            $('#modal_ram').val(row.data('ram_option_id') || '');
            $('#modal_storage').val(row.data('storage_option_id') || '');
            $('#modal_color_option_id').val(row.data('color_option_id') || '');
            $('#modal_battery_percentage').val(row.data('battery'));
            $('#modal_condition_grade').val(row.data('condition'));
            $('#modal_status').val(row.data('status'));
            $('#modal_purchase_price').val(row.data('purchase_price') || '');
            $('#modal_selling_price').val(row.data('selling_price') || '');
            $('#modal_warranty_id').val(row.data('warranty_id') || '');
            $('#modal_image_preview_wrapper').empty();
            let imagesData = row.data('image');
            if (imagesData) {
                let parsedImages = [];
                try {
                    parsedImages = typeof imagesData === 'string' ? JSON.parse(imagesData) : imagesData;
                } catch(e) { parsedImages = [imagesData]; }
                
                if (Array.isArray(parsedImages)) {
                    parsedImages.forEach(img => {
                        if(img) {
                           $('#modal_image_preview_wrapper').append(`
                                <div class="image-preview-item">
                                    <img src="{{ asset('storage/devices') }}/${img}" width="100" alt="Device">
                                    <input type="hidden" name="image[]" value="${img}">
                                    <button type="button" class="btn btn-danger btn-sm remove-image-btn"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                            `); 
                        }
                    });
                }
            }
            $('#device-edit-form').attr('action', updateDeviceUrl + '/' + deviceId);
            deviceEditModal.show();
        });


        $('#modal_image_input').on('change', function(e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                Array.from(files).forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        cropperImage.src = e.target.result;
                        cropperModal.show();
                        $('#crop-button').off('click').on('click', function() {
                            if (!cropper) return;
                            const canvas = cropper.getCroppedCanvas({ width: 600, height: 600 });
                            canvas.toBlob(function(blob) {
                                const reader2 = new FileReader();
                                reader2.readAsDataURL(blob);
                                reader2.onloadend = function() {
                                    const base64data = reader2.result;
                                    $('#modal_image_preview_wrapper').append(`
                                        <div class="image-preview-item">
                                            <img src="${base64data}" width="100">
                                            <input type="hidden" name="image[]" value="${base64data}">
                                            <button type="button" class="btn btn-danger btn-sm remove-image-btn"><i class="fa-solid fa-xmark"></i></button>
                                        </div>
                                    `);
                                    cropperModal.hide();
                                    
                                    // Reset file input
                                    $('#modal_image_input').val('');
                                };
                            }, 'image/jpeg');
                        });
                    };
                    reader.readAsDataURL(file);
                });
            }
        });

        $('#modal_image_preview_wrapper').on('click', '.remove-image-btn', function() {
            $(this).closest('.image-preview-item').remove();
        });

        $('#device-edit-form').on('submit', function(e) {
            e.preventDefault();
            const deviceId = $('#device_edit_id').val();
            let formData = new FormData(this);
            $.ajax({
                url: "{{ url('admin/device/update') }}/" + deviceId,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    deviceEditModal.hide();
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: response.message, timer: 2000, showConfirmButton: false });
                    const row = $(`tr[data-device-id="${deviceId}"]`);
                    row.data('imei', $('#modal_imei').val());
                    row.data('ram_option_id', $('#modal_ram').val());
                    row.data('storage_option_id', $('#modal_storage').val());
                    row.data('color_option_id', $('#modal_color_option_id').val());
                    row.data('purchase_price', $('#modal_purchase_price').val());
                    row.data('selling_price', $('#modal_selling_price').val());
                    row.data('warranty_id', $('#modal_warranty_id').val());

                    let imageArray = [];
                    $('#modal_image_preview_wrapper input[name="image[]"]').each(function() {
                        let val = $(this).val();
                        if (val.startsWith('data:image')) {
                             imageArray.push('pending_reload'); 
                        } else {
                             imageArray.push(val);
                        }
                    });
                    row.data('image', JSON.stringify(imageArray));

                    row.data('battery', $('#modal_battery_percentage').val());
                    row.data('condition', $('#modal_condition_grade').val());
                    row.data('status', $('#modal_status').val());
                    row.find('.device-imei').text($('#modal_imei').val());
                    row.find('.device-ram').text($('#modal_ram option:selected').text());
                    row.find('.device-storage').text($('#modal_storage option:selected').text());
                    row.find('.device-color').text($('#modal_color_option_id option:selected').text());
                    row.find('.device-status').removeClass('bg-success bg-secondary bg-warning')
                        .addClass($('#modal_status').val() == 'available' ? 'bg-success' : ($('#modal_status').val() == 'sold' ? 'bg-secondary' : 'bg-warning'))
                        .text($('#modal_status').val());
                },
                error: function(xhr) {
                    let msg = xhr.responseJSON?.message || 'Something went wrong';
                    if (xhr.responseJSON?.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    }
                    Swal.fire({ icon: 'error', title: 'Error', html: msg });
                }
            });
        });
    });
</script>
@endsection
