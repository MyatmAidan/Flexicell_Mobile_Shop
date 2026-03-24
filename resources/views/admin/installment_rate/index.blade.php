@extends('layouts.app')

@section('meta')
    <meta name="description" content="Installment Plan List">
    <meta name="keywords" content="Installment Plan, List, Flexicell">
@endsection

@section('title', 'Installment Plan List')


@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                        <h1 class="card-title h5 d-flex align-items-center gap-2 mb-4">
                            Installment Plan Lists
                        </h1>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createInstallmentModal">
                            <i class="fas fa-plus"></i> Create New Installment Plan
                        </button>
                    </div>
                    <div class="">
                        <table class="table table-hover table-sm" id="datatable">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>No</th>
                                    <th>Installment Month</th>
                                    <th>Installment Rate</th>
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

    <!-- Create Installment Plan Modal -->
<div class="modal fade" id="createInstallmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <form id="createInstallmentForm">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Create Installment Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Installment Month</label>
                        <input type="number" name="installment_month" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Installment Rate</label>
                        <input type="number" name="installment_rate" class="form-control" required>
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
<form method="POST" id="editForm" action="{{ route('admin.installment_rate.update', ':id') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="editModalLabel">Edit Installment Plan</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="edit_installment_month">Installment Month</label>
                            <input type="text" class="form-control" id="edit_installment_month" name="installment_month" placeholder="Enter installment month" />
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit_installment_rate">Installment Rate</label>
                            <input type="text" class="form-control" id="edit_installment_rate" name="installment_rate" placeholder="Enter installment rate" />
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
                ajax: "{{ route('admin.installment_rate.getList') }}",
                columns: [
                    { 
                        data: 'plus-icon',
                        name: 'plus-icon',
                        className: 'dt-control',
                        orderable: false,
                        searchable: false,
                    },
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { 
                        data: 'month_option', 
                        name: 'installment_month',
                    },
                    { 
                        data: 'rate', 
                        name: 'installment_rate',
                        className: 'text-center'
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
                order: [[4, 'desc']],
            });


            // Create Installment Plan
            $('#createInstallmentForm').submit(function (e) {
                e.preventDefault();

                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('admin.installment_rate.store') }}",
                    method: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (res) {
                        $('#createInstallmentModal').modal('hide');
                        $('#createInstallmentForm')[0].reset();

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Success',
                            text: res.message ?? 'Installment plan created successfully',
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


            //edit installment plan
            let updateUrlTemplate = "{{ route('admin.installment_rate.update', ':id') }}";
            $(document).on('click', '.edit-installment-btn', function (e) {
                e.preventDefault();
                let id    = $(this).data('id');
                let installment_month  = $(this).data('installmentMonth');
                let installment_rate = $(this).data('installmentRate');
                

                // populate modal fields
                $('#edit_id').val(id);
                $('#edit_installment_month').val(installment_month);
                $('#edit_installment_rate').val(installment_rate);

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
                            text: res.message ?? 'Installment plan updated successfully',
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

                let installment_id = $(this).data('id');

                if (!installment_id) {
                    console.error('Installment ID missing');
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

                    let destroyUrl = "{{ route('admin.installment_rate.destroy', '__id__') }}";
                    destroyUrl = destroyUrl.replace('__id__', installment_id);

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
                                        'Installment plan deleted successfully.',
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
                                        'Failed to delete Installment plan.',
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