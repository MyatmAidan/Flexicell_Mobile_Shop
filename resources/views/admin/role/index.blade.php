@extends('layouts.app')

@section('title', 'Roles List')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                    <h1 class="card-title h5 d-flex align-items-center gap-2 mb-4">
                        <i class="fas fa-user-tag text-primary"></i>
                        Roles Management
                    </h1>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                        <i class="fas fa-plus"></i> Create New Role
                    </button>
                </div>
                <div class="table-responsive" style="overflow-x: visible;">
                    <table class="table table-hover table-sm w-100" id="role-datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Role Modal -->
<div class="modal fade" id="createRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="createRoleForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create New Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Role Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Sales Staff" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role Code (Optional)</label>
                        <input type="text" name="code" class="form-control" placeholder="e.g. sales-staff">
                        <small class="text-muted">Slug will be generated from name if left empty.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Role</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function () {
    let datatable = $('#role-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.role.getList') }}",
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'code', name: 'code' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ]
    });

    $('#createRoleForm').submit(function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        
        $.ajax({
            url: "{{ route('admin.role.store') }}",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                $('#createRoleModal').modal('hide');
                $('#createRoleForm')[0].reset();
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: res.message,
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
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
        let id = $(this).data('id');

        Swal.fire({
            title: 'Delete role?',
            text: "This cannot be undone and will fail if users are assigned.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: "{{ url('admin/role') }}/" + id,
                method: "POST",
                data: { _token: "{{ csrf_token() }}", _method: "DELETE" },
                success: function (res) {
                    if (res.status) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: res.message,
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true,
                        });
                        datatable.ajax.reload(null, false);
                    } else {
                        Swal.fire({ icon: 'error', title: 'Forbidden', text: res.message });
                    }
                },
                error: function (xhr) {
                    Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Delete failed' });
                }
            });
        });
    });
});
</script>
@endsection
