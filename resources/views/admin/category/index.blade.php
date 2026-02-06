@extends('layouts.app')

@section('meta')
    <meta name="description" content="Category List">
    <meta name="keywords" content="Category, List, Flexicell">
@endsection

@section('title', 'Category List')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                        <h1 class="card-title h5 d-flex align-items-center gap-2 mb-4">
                            Category Lists
                        </h1>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                            <i class="fas fa-plus"></i> Create New Category
                        </button>
                    </div>
                    <div class="">
                        <table class="table table-hover table-sm" id="datatable">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>#</th>
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

    <!-- Create Category Modal -->
    <div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <form id="createCategoryForm">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">Create Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Category Name</label>
                            <input type="text" name="category_name" class="form-control" required>
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

    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" id="editForm" action="" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="editModalLabel">Edit Category</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_name">Category Name</label>
                            <input type="text" class="form-control" id="edit_name" name="category_name" placeholder="Enter category name" />
                        </div>
                        <input type="hidden" id="edit_id" name="id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection



@section('script')
   <script>
        $(document).ready(function () {

            let datatable = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.category.getList') }}",
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
                        data: 'category_name', 
                        name: 'category_name'
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
                order: [[3, 'desc']],
            });

            $('#createCategoryModal').on('hidden.bs.modal', function () {
                $('#createCategoryForm')[0].reset();
                $('#logoPreview').hide().attr('src', '');
            });

            // Create Category
            $('#createCategoryForm').submit(function (e) {
                e.preventDefault();

                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('admin.category.store') }}",
                    method: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (res) {
                        $('#createCategoryModal').modal('hide');
                        $('#createCategoryForm')[0].reset();

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Success',
                            text: res.message ?? 'Category created successfully',
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });

                        datatable.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        let msg = 'Something went wrong';
                        if (xhr.responseJSON?.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join(' ');
                        } else if (xhr.responseJSON?.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: msg
                        });
                    }
                });
            });


            //edit category
            let updateUrlTemplate = "{{ route('admin.category.update', ':id') }}";
            $(document).on('click', '.edit-category-btn', function (e) {
                e.preventDefault();

                let id   = $(this).data('id');
                let name = $(this).data('name');

                $('#edit_id').val(id);
                $('#edit_name').val(name);

                let updateUrl = updateUrlTemplate.replace(':id', id);
                $('#editForm').attr('action', updateUrl);
            });


            $('#editForm').submit(function (e) {
                e.preventDefault();

                let formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
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
                            text: res.message ?? 'Category updated successfully',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        datatable.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        let msg = 'Something went wrong';
                        if (xhr.responseJSON?.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join(' ');
                        } else if (xhr.responseJSON?.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: msg
                        });
                    }
                });
            });


            $(document).on('click', '.delete-btn', function (e) {
                e.preventDefault();

                let category_id = $(this).data('id');

                if (!category_id) {
                    console.error('Category ID missing');
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

                    let destroyUrl = "{{ route('admin.category.destroy', '__id__') }}";
                    destroyUrl = destroyUrl.replace('__id__', category_id);

                    $.ajax({
                        url: destroyUrl,
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: "DELETE"
                        },
                        success: function (response) {
                            if (response.message) {
                                Swal.fire({
                                    toast: true,
                                    position: "top-end",
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message || 'Category deleted successfully.',
                                    timer: 2000,
                                    showConfirmButton: false,
                                    timerProgressBar: true,
                                });
                                datatable.ajax.reload(null, false);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Failed to delete Category.',
                                });
                            }
                        },

                        error: function (xhr) {
                            console.error(xhr.responseText);

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
