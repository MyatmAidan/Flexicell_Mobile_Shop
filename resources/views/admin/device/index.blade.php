@extends('layouts.app')

@section('meta')
    <meta name="description" content="Device List">
    <meta name="keywords" content="Device, List, Flexicell">
@endsection

@section('title', 'Device List')

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
                            <i class="fas fa-plus"></i> Create New Device
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
                                    <th>Color</th>
                                    <th>Status</th>
                                    <th>Created at</th>
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
                { data: 'color', name: 'color' },
                { data: 'status', name: 'status' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            order: [[9, 'desc']],
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
                    $('#modal_ram').val(data.device.ram);
                    $('#modal_storage').val(data.device.storage);
                    $('#modal_color').val(data.device.color);
                    $('#modal_battery_percentage').val(data.device.battery_percentage);
                    $('#modal_condition_grade').val(data.device.condition_grade);
                    $('#modal_status').val(data.device.status);
                    deviceEditModal.show();
                })
                .fail(function() {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load device data' });
                });
        });

        $('#device-edit-form').on('submit', function (e) {
            e.preventDefault();
            const deviceId = $('#device_edit_id').val();
            $.ajax({
                url: updateDeviceUrl + '/' + deviceId,
                method: 'POST',
                data: $(this).serialize(),
                success: function (response) {
                    deviceEditModal.hide();
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: response.message,
                        timer: 2000,
                        showConfirmButton: false
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
    });
</script>
@endsection