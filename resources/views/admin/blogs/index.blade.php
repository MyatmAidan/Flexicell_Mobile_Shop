@extends('layouts.app')

@section('meta')
    <meta name="description" content="Blog List">
    <meta name="keywords" content="Blog, List, Flexicell">
@endsection

@section('title', 'Blog List')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                        <h1 class="card-title h5 d-flex align-items-center gap-2 mb-0">
                            Blog List
                        </h1>
                        <a href="{{ route('admin.blogs.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Create New Blog
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover table-sm" id="blog-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Thumbnail</th>
                                    <th>Title</th>
                                    <th>Sections</th>
                                    <th>Created At</th>
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
        let datatable = $('#blog-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.blogs.getList') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'thumbnail', name: 'thumbnail', orderable: false, searchable: false },
                { data: 'title', name: 'title' },
                { data: 'sections', name: 'sections', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            order: [[4, 'desc']],
        });

        $(document).on('click', '.delete-btn', function (e) {
            e.preventDefault();
            let blogId = $(this).data('id');
            if (!blogId) return;

            Swal.fire({
                title: 'Are you sure?',
                text: "This will permanently delete the blog and all its sections & images.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (!result.isConfirmed) return;

                let destroyUrl = "{{ route('admin.blogs.destroy', '__id__') }}".replace('__id__', blogId);
                $.ajax({
                    url: destroyUrl,
                    method: "POST",
                    data: { _token: "{{ csrf_token() }}", _method: "DELETE" },
                    success: function (response) {
                        if (response.status) {
                            Swal.fire({
                                toast: true,
                                position: "top-end",
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message || 'Blog deleted successfully.',
                                timer: 2000,
                                showConfirmButton: false,
                                timerProgressBar: true,
                            });
                            datatable.ajax.reload(null, false);
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Delete failed' });
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
