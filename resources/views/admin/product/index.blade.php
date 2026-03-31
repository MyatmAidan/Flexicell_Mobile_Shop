@extends('layouts.app')

@section('meta')
    <meta name="description" content="Product List">
    <meta name="keywords" content="Product, List, Flexicell">
@endsection

@section('title', 'Product List')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                        <h1 class="card-title h5 d-flex align-items-center gap-2 mb-4">
                            Product List
                        </h1>
                        <a href="{{ route('admin.product.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Add New Product
                        </a>
                    </div>
                    <div class="">
                        <table class="table table-hover table-sm" id="product-table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Model</th>
                                    <th>Brand</th>
                                    <th>Type</th>
                                    <th>Stock</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    $(document).ready(function () {
        let datatable = $('#product-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.product.getList') }}",
            columns: [
                { data: 'plus-icon', name: 'plus-icon', className: 'dt-control', orderable: false, searchable: false },
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'image', name: 'image', orderable: false, searchable: false },
                { data: 'model_name', name: 'model_name' },
                { data: 'brand_name', name: 'brand_name' },
                { data: 'product_type', name: 'product_type' },
                { data: 'available_devices_count', name: 'available_devices_count' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            order: [[6, 'desc']],
        });

        $(document).on('click', '.delete-btn', function (e) {
            e.preventDefault();
            let productId = $(this).data('id');
            if (!productId) return;

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

                let destroyUrl = "{{ route('admin.product.destroy', '__id__') }}".replace('__id__', productId);
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
                            text: response.message || 'Product deleted successfully.',
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
