@extends('layouts.app')

@section('meta')
    <meta name="description" content="Category List">
    <meta name="keywords" content="Category, List, Flexicell">
@endsection

@section('title', 'Category List')
@section('style')
<style>
    .color-picker-container {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .color-option {
        width: 40px;
        height: 40px;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid transparent;
        transition: transform 0.1s;
    }
    .color-option.selected {
        transform: scale(1.1);
        box-shadow: 0 0 5px rgba(0,0,0,0.3);
    }
    .color-option.selected::after {
        content: '\f00c';
        font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", "FontAwesome";
        font-weight: 900;
        color: white;
        text-shadow: 0 0 3px rgba(0,0,0,0.8);
    }
</style>
@endsection
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
                                    <th>Color</th>
                                    <th>Name</th>
                                    <th>Product Counts</th>
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
                        <div class="mb-3">
                            <label class="form-label">Category Color</label>
                            <div class="color-picker-container" id="create_color_picker">
                                <div class="color-option" data-color="#E0E0E0" style="background-color: #E0E0E0;"></div>
                                <div class="color-option" data-color="#F44336" style="background-color: #F44336;"></div>
                                <div class="color-option" data-color="#E91E63" style="background-color: #E91E63;"></div>
                                <div class="color-option" data-color="#FF9800" style="background-color: #FF9800;"></div>
                                <div class="color-option" data-color="#FFEB3B" style="background-color: #FFEB3B;"></div>
                                <div class="color-option" data-color="#4CAF50" style="background-color: #4CAF50;"></div>
                                <div class="color-option" data-color="#2196F3" style="background-color: #2196F3;"></div>
                                <div class="color-option" data-color="#9C27B0" style="background-color: #9C27B0;"></div>
                            </div>
                            <input type="hidden" name="color" id="create_color_input" value="">
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
                        <div class="form-group mb-3">
                            <label for="edit_name">Category Name</label>
                            <input type="text" class="form-control" id="edit_name" name="category_name" placeholder="Enter category name" required/>
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit_color">Category Color</label>
                            <div class="color-picker-container" id="edit_color_picker">
                                <div class="color-option" data-color="#E0E0E0" style="background-color: #E0E0E0;"></div>
                                <div class="color-option" data-color="#F44336" style="background-color: #F44336;"></div>
                                <div class="color-option" data-color="#E91E63" style="background-color: #E91E63;"></div>
                                <div class="color-option" data-color="#FF9800" style="background-color: #FF9800;"></div>
                                <div class="color-option" data-color="#FFEB3B" style="background-color: #FFEB3B;"></div>
                                <div class="color-option" data-color="#4CAF50" style="background-color: #4CAF50;"></div>
                                <div class="color-option" data-color="#2196F3" style="background-color: #2196F3;"></div>
                                <div class="color-option" data-color="#9C27B0" style="background-color: #9C27B0;"></div>
                            </div>
                            <input type="hidden" name="color" id="edit_color" value="">
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
                        data: 'color', 
                        name: 'color',
                        className: 'text-center',
                        orderable: false,
                    },
                    { 
                        data: 'category_name', 
                        name: 'category_name',
                        searchable: true
                    },
                    { 
                        data: 'products_count', 
                        name: 'products_count',
                        className: 'text-center',
                        searchable: false
                    },
                    {   
                        data: 'created_at', 
                        name: 'created_at',
                        searchable: false
                    },
                    { 
                        data: 'action', 
                        name: 'action',
                        orderable: false, 
                        searchable: false 
                    },
                ],
                order: [[4, 'desc']],
            });

            $('#createCategoryModal').on('hidden.bs.modal', function () {
                $('#createCategoryForm')[0].reset();
                $('#logoPreview').hide().attr('src', '');
                $('#create_color_picker .color-option').removeClass('selected');
                $('#create_color_input').val('');
            });

            // Handle color picker selection
            $(document).on('click', '.color-option', function() {
                let container = $(this).closest('.color-picker-container');
                container.find('.color-option').removeClass('selected');
                $(this).addClass('selected');
                container.next('input[name="color"]').val($(this).data('color'));
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

                let id    = $(this).data('id');
                let name  = $(this).data('name');
                let color = $(this).data('color') || '#000000';

                $('#edit_id').val(id);
                $('#edit_name').val(name);
                $('#edit_color').val(color);
                $('#edit_color_picker .color-option').removeClass('selected');
                if (color) {
                    $('#edit_color_picker .color-option[data-color="'+color+'"]').addClass('selected');
                }

                let updateUrl = updateUrlTemplate.replace(':id', id);
                $('#editForm').attr('action', updateUrl);

                $('#editModal').modal('show');
            });

            $('#editModal').on('hidden.bs.modal', function () {
                $('#editForm')[0].reset();
                $('#edit_id').val('');
                $('#edit_color_picker .color-option').removeClass('selected');
                $('#edit_color').val('');
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
