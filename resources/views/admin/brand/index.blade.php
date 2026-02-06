@extends('layouts.app')

@section('meta')
    <meta name="description" content="Brand List">
    <meta name="keywords" content="Brand, List, Flexicell">
@endsection

@section('title', 'Brand List')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                        <h1 class="card-title h5 d-flex align-items-center gap-2 mb-4">
                            Brand Lists
                        </h1>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createBrandModal">
                            <i class="fas fa-plus"></i> Create New Brand
                        </button>
                    </div>
                    <div class="">
                        <table class="table table-hover table-sm" id="datatable">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>#</th>
                                    <th>Logo</th>
                                    <th>Name</th>
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

    <!-- Create Brand Modal -->
<div class="modal fade" id="createBrandModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <form id="createBrandForm">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Create Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Brand Name</label>
                        <input type="text" name="brand_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Logo</label>
                        <input type="file" name="logo" class="form-control" id="create_logo" accept="image/*">
                    </div>

                    <div class="mb-3 text-center">
                        <img id="logoPreview"
                            src=""
                            alt="Logo Preview"
                            class="img-fluid rounded border"
                            style="max-height: 150px; display: none;">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Brand Modal -->
<form method="POST" id="editForm" action="{{ route('admin.brand.update', ':id') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="editModalLabel">Edit Brand</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_name">Brand Name</label>
                            <input type="text" class="form-control" id="edit_name" name="brand_name" placeholder="Enter brand name" />
                        </div>
                        <div class="form-group mt-3">
                            <label for="edit_logo">Logo</label>
                            <input type="file" class="form-control" id="edit_logo" name="logo" accept="image/*" />
                            <img src="" alt="Current Logo" id="edit_brand_logo" class="mt-2 d-none" style="max-width: 100px; max-height: 100px;">
                        </div>
                        <input type="hidden" id="edit_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection



@section('script')
   <script>
        $(document).ready(function () {

            let datatable = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.brand.getList') }}",
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
                        data: 'logo', 
                        name: 'logo',
                    },
                    { 
                        data: 'brand_name', 
                        name: 'brand_name'
                    },
                    {   data: 'created_at', 
                        name: 'created_at',
                    },
                    { 
                        data: 'action', 
                        name: 'action',
                        orderable: false, 
                        searchable: false 
                    },
                ],
                order: [[5, 'desc']],
            });

            // Create brand logo preview
            $(document).on('change', '#create_logo', function () {
                const file = this.files[0];
                const preview = $('#logoPreview');

                if (!file) {
                    preview.hide().attr('src', '');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.attr('src', e.target.result).show();
                };
                reader.readAsDataURL(file);
            });

            $('#createBrandModal').on('hidden.bs.modal', function () {
                $('#createBrandForm')[0].reset();
                $('#logoPreview').hide().attr('src', '');
            });

            // Create Brand
            $('#createBrandForm').submit(function (e) {
                e.preventDefault();

                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('admin.brand.store') }}",
                    method: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (res) {
                        $('#createBrandModal').modal('hide');
                        $('#createBrandForm')[0].reset();

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Success',
                            text: res.message ?? 'Brand created successfully',
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });

                        datatable.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Something went wrong'
                        });
                    }
                });
            });

            // Edit brand logo preview
            $(document).on('change', '#edit_logo', function () {
                const file = this.files[0];
                const preview = $('#edit_brand_logo');

                if (!file) {
                    preview.addClass('d-none').attr('src', '');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    preview
                        .attr('src', e.target.result)
                        .removeClass('d-none');
                };
                reader.readAsDataURL(file);
            });

            $('#editModal').on('hidden.bs.modal', function () {
                $('#editForm')[0].reset();
                $('#edit_brand_logo').addClass('d-none').attr('src', '');
                $('#edit_id').val('');
            });

            //edit brand
            let updateUrlTemplate = "{{ route('admin.brand.update', ':id') }}";
            $(document).on('click', '.edit-brand-btn', function (e) {
                e.preventDefault();
                let id   = $(this).data('id');
                let name = $(this).data('name');
                let logo = $(this).data('logo');
                

                // populate modal fields
                $('#edit_id').val(id);
                $('#edit_name').val(name);
                $('#edit_logo_name').val($(this).data('logo'));

                if (logo) {
                    $('#edit_brand_logo').attr('src', logo).removeClass('d-none');
                } else {
                    $('#edit_brand_logo').addClass('d-none');
                }

                $('#editModal').modal('show');
            });


            $('#editForm').submit(function (e) {
                e.preventDefault();

                let formData = new FormData(this);
                console.log(formData);
                
                let id = $('#edit_id').val();

                let updateUrl = updateUrlTemplate.replace(':id', id);

                $.ajax({
                    url: updateUrl,
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (res) {
                        $('#editModal').modal('hide');
                        $('#editForm')[0].reset();

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Success',
                            text: res.message ?? 'Brand updated successfully',
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });

                        datatable.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Something went wrong'
                        });
                    }
                });
            });

            $(document).on('click', '.delete-btn', function (e) {
                e.preventDefault();

                let brand_id = $(this).data('id');

                if (!brand_id) {
                    console.error('Brand ID missing');
                    return;
                }

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

                    let destroyUrl = "{{ route('admin.brand.destroy', '__id__') }}";
                    destroyUrl = destroyUrl.replace('__id__', brand_id);

                    $.ajax({
                        url: destroyUrl,
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: "DELETE"
                        },
                        success: function (response) {
                            if(response.status){
                                Swal.fire({
                                    toast: true,
                                    position: "top-end",
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message ||
                                        'Brand deleted successfully.',
                                    timer: 2000,
                                    showConfirmButton: false,
                                    timerProgressBar: true,
                                });
                                datatable.ajax.reload(null, false);
                            }else{
                                 Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message ||
                                        'Failed to delete Brand.',
                                });
                            }
                        },
                        error: function (xhr) {
                            console.error(xhr.responseText);

                            Toast.fire({
                                icon: 'error',
                                title: xhr.responseJSON?.message || 'Delete failed'
                            });
                        }
                    });
                });
            });
        });
</script>

@endsection
