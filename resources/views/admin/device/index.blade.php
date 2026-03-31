@extends('layouts.app')

@section('meta')
    <meta name="description" content="Device List">
    <meta name="keywords" content="Device, List, Flexicell">
@endsection

@section('title', 'Device List')
@section('style')
<style>
    .image-preview-item { display: inline-block; margin: 5px; position: relative; }
    .image-preview-item img { border: 1px solid #ddd; border-radius: 4px; }
    .image-preview-item .remove-image-btn { position: absolute; top: -5px; right: -5px; border-radius: 50%; width: 20px; height: 20px; padding: 0; font-size: 10px; }
</style>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                        <h1 class="card-title h5 d-flex align-items-center gap-2 mb-4">
                            Device List
                        </h1>
                        <a href="{{ route('admin.device.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Add New Device
                        </a>
                    </div>
                    <div class="">
                        <table class="table table-hover table-sm" id="device-table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Brand</th>
                                    <th>IMEI</th>
                                    <th>RAM</th>
                                    <th>Storage</th>
                                    <th class="text-center">Color</th>
                                    <th>Status</th>
                                    <th>Purchase P.</th>
                                    <th>Selling P.</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
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
        let datatable = $('#device-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.device.getList') }}",
            columns: [
                { data: 'plus-icon', name: 'plus-icon', className: 'dt-control', orderable: false, searchable: false },
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'product_name', name: 'product_name' },
                { data: 'brand_name', name: 'brand_name' },
                { data: 'imei', name: 'imei' },
                { data: 'ram', name: 'ram' },
                { data: 'storage', name: 'storage' },
                { data: 'color', name: 'color', className: 'text-center' },
                { data: 'status', name: 'status' },
                { data: 'purchase_price', name: 'purchase_price' },
                { data: 'selling_price', name: 'selling_price' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            order: [[8, 'desc']],
        });

        const deviceEditModal = new bootstrap.Modal(document.getElementById('deviceEditModal'));
        const getDataUrl = "{{ route('admin.device.getData', '__id__') }}";
        const updateDeviceUrl = "{{ url('admin/device/update') }}";

        $(document).on('click', '.edit-device-btn', function (e) {
            e.preventDefault();
            const deviceId = $(this).data('id');
            $.get(getDataUrl.replace('__id__', deviceId))
                .done(function(data) {
                    $('#device_edit_id').val(data.device.id);
                    $('#modal_product_id').empty().append('<option value="">Select Product</option>');
                    data.products.forEach(function(p) {
                        $('#modal_product_id').append(`<option value="${p.id}" ${data.device.product_id == p.id ? 'selected' : ''}>${p.label}</option>`);
                    });
                    $('#modal_imei').val(data.device.imei);
                    $('#modal_ram').val(data.device.ram_option_id || '');
                    $('#modal_storage').val(data.device.storage_option_id || '');
                    $('#modal_color_option_id').val(data.device.color_option_id || '');
                    $('#modal_battery_percentage').val(data.device.battery_percentage);
                    $('#modal_condition_grade').val(data.device.condition_grade);
                    $('#modal_status').val(data.device.status);
                    $('#modal_purchase_price').val(data.device.purchase_price);
                    $('#modal_selling_price').val(data.device.selling_price);
                    $('#modal_warranty_id').val(data.device.warranty_id || '');

                    $('#modal_image_preview_wrapper').empty();
                    let images = data.device.image || [];
                    if (!Array.isArray(images)) images = [images];
                    images.forEach(img => {
                        if (img) {
                             $('#modal_image_preview_wrapper').append(`
                                <div class="image-preview-item">
                                    <img src="{{ asset('storage/devices') }}/${img}" width="100" alt="Device">
                                    <input type="hidden" name="image[]" value="${img}">
                                    <button type="button" class="btn btn-danger btn-sm remove-image-btn"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                            `); 
                        }
                    });

                    deviceEditModal.show();
                })
                .fail(function() {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load device data' });
                });
        });

        // Color select -> color input sync
        $(document).on('change', '#modal_color_select', function () {
            const v = $(this).val();
            if (v) {
                $('#modal_color').val(v);
            }
        });

        $('#device-edit-form').on('submit', function (e) {
            e.preventDefault();
            const deviceId = $('#device_edit_id').val();
            let formData = new FormData(this);
            $.ajax({
                url: updateDeviceUrl + '/' + deviceId,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    deviceEditModal.hide();
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                    });
                    datatable.ajax.reload(null, false);
                },
                error: function (xhr) {
                    let msg = xhr.responseJSON?.message || 'Something went wrong';
                    if (xhr.responseJSON?.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    }
                    Swal.fire({ icon: 'error', title: 'Error', html: msg });
                }
            });
        });

        $(document).on('click', '.delete-btn', function (e) {
            e.preventDefault();
            let deviceId = $(this).data('id');
            if (!deviceId) return;

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (!result.isConfirmed) return;

                let destroyUrl = "{{ route('admin.device.destroy', '__id__') }}".replace('__id__', deviceId);
                $.ajax({
                    url: destroyUrl,
                    method: "POST",
                    data: { _token: "{{ csrf_token() }}", _method: "DELETE" },
                    success: function (response) {
                        Swal.fire({
                            toast: true,
                            position: "top-end",
                            icon: 'success',
                            title: 'Success',
                            text: response.message || 'Device deleted successfully.',
                            timer: 2000,
                            showConfirmButton: false,
                            timerProgressBar: true,
                        });
                        datatable.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Delete failed'
                        });
                    }
                });
            });
        });

        // Cropper logic
        let cropper;
        let cropperModal = new bootstrap.Modal(document.getElementById('cropper-modal'));
        const cropperImage = document.getElementById('cropper-image');

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

        $('#cropper-modal').on('shown.bs.modal', function() {
            cropper = new Cropper(cropperImage, { aspectRatio: 1, viewMode: 1 });
        }).on('hidden.bs.modal', function() {
            if (cropper) { cropper.destroy(); cropper = null; }
        });

        $('#modal_image_preview_wrapper').on('click', '.remove-image-btn', function() {
            $(this).closest('.image-preview-item').remove();
        });
    });
</script>
@endsection