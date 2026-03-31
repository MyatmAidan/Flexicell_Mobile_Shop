@extends('layouts.app')

@section('meta')
    <meta name="description" content="Phone Model List">
    <meta name="keywords" content="Phone Model, List, Flexicell">
@endsection

@section('title', 'Phone Model List')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap" style="padding: 15px;">
                            <h3 class="card-title">Phone Model List</h3>
                            <div class="card-tools">
                                <a href="{{ route('admin.phone_model.create') }}" class="btn btn-success btn-md">
                                    <i class="fas fa-plus"></i> Add New Phone Model
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="phone-model-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th></th> 
                                        <th>#</th>
                                        <th>Model Name</th>
                                        <th>Brand</th>
                                        <th>Category</th>
                                        <th>Release Year</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#phone-model-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.phone_model.getList') }}',
                columns: [
                    { 
                        data: 'plus-icon',
                        name: 'plus-icon',
                        className: 'dt-control',
                        orderable: false,
                        searchable: false,
                    },
                    { 
                        data: 'DT_RowIndex',
                        orderable: false, 
                        searchable: false 
                    },
                    { 
                        data: 'model_name', 
                        name: 'model_name' 
                    },
                    { 
                        data: 'brand_name', 
                        name: 'brand_name' 
                    },
                    { 
                        data: 'category_name', 
                        name: 'category_name' 
                    },
                    { 
                        data: 'release_year', 
                        name: 'release_year' 
                    },
                    { 
                        data: 'actions', 
                        name: 'actions', 
                        orderable: false, 
                        searchable: false 
                    }
                ]
            });

            // Edit button handler
            $(document).on('click', '.edit-phone-model-btn', function() {
                let id = $(this).data('id');
                window.location.href = "{{ url('admin/phone-model/edit') }}/" + id;
            });

            // View button handler
            $(document).on('click', '.view-phone-model-btn', function() {
                let id = $(this).data('id');
                window.location.href = "{{ url('admin/phone-model/show') }}/" + id;
            });

            // Delete button handler
            $(document).on('click', '.delete-btn', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('admin/phone-model') }}/" + id,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: response.message || 'Deleted',
                                    showConfirmButton: false,
                                    timer: 2000,
                                    timerProgressBar: true,
                                }).then(() => {
                                    $('#phone-model-table').DataTable().ajax.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    'Something went wrong.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection