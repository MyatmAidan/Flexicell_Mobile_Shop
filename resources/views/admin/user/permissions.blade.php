@extends('layouts.app')

@section('title', 'Manage Permissions — ' . $user->name)

@section('style')
<style>
    .permission-grid thead th {
        background-color: #f8f9fa;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        text-align: center;
        border-bottom: 2px solid #dee2e6;
    }
    .permission-grid tbody td {
        vertical-align: middle;
    }
    .module-name {
        font-weight: 600;
        color: #495057;
    }
    .override-group {
        display: flex;
        justify-content: center;
    }
    .btn-check:checked + .btn-outline-secondary { background-color: #6c757d; color: #fff; }
    .btn-check:checked + .btn-outline-success { background-color: #198754; color: #fff; }
    .btn-check:checked + .btn-outline-danger { background-color: #dc3545; color: #fff; }
    
    .other-perm-row {
        border-bottom: 1px solid #eee;
        padding: 8px 0;
    }
    .other-perm-row:last-child { border-bottom: 0; }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">

                {{-- Header --}}
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h1 class="h5 mb-0 d-flex align-items-center gap-2">
                            <i class="fas fa-user-shield text-primary"></i>
                            Custom Permissions — <span class="fw-bold">{{ $user->name }}</span>
                        </h1>
                        <small class="text-muted">
                            Role: <span class="badge bg-{{ $user->role === 'superadmin' ? 'danger' : ($user->role === 'manager' ? 'warning text-dark' : 'secondary') }}">{{ ucfirst($user->role) }}</span>
                            &nbsp;{{ $user->email }}
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.user.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Users
                        </a>
                        <button type="submit" form="permissionForm" class="btn btn-primary btn-sm px-3" id="saveBtn">
                            <i class="fas fa-save"></i> Save Permissions
                        </button>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-8">
                        {{-- Legend --}}
                        <div class="alert alert-light border d-flex gap-3 align-items-center py-2 mb-0">
                            <i class="fas fa-info-circle text-info"></i>
                            <div class="small">
                                <strong>Inherit</strong> = role default &nbsp;|&nbsp;
                                <strong class="text-success">Grant</strong> = force allow &nbsp;|&nbsp;
                                <strong class="text-danger">Revoke</strong> = force deny
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" id="permSearch" class="form-control" placeholder="Search modules...">
                        </div>
                    </div>
                </div>

                {{-- Permission Table --}}
                <form id="permissionForm">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle permission-grid">
                            <thead>
                                <tr>
                                    <th class="text-start" style="width: 220px;">Module / Section</th>
                                    <th style="width: 240px;">View</th>
                                    <th style="width: 240px;">Create</th>
                                    <th style="width: 240px;">Edit</th>
                                    <th style="width: 240px;">Delete</th>
                                    <th>Other Actions Override</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groupedPermissions as $module => $data)
                                <tr>
                                    <td class="module-name px-3">
                                        {{ $data['displayName'] }}
                                    </td>
                                    
                                    @foreach(['view', 'create', 'edit', 'delete'] as $action)
                                    <td class="text-center p-1">
                                        @if($data[$action])
                                            @include('admin.user.partials.override-buttons', ['perm' => $data[$action]])
                                        @else
                                            <span class="text-muted opacity-25 small">—</span>
                                        @endif
                                    </td>
                                    @endforeach

                                    <td class="p-0">
                                        @if(!empty($data['other']))
                                            @foreach($data['other'] as $other)
                                                <div class="other-perm-row px-3 d-flex align-items-center justify-content-between">
                                                    <span class="small text-muted text-capitalize me-2">
                                                        {{ str_replace($module.'.', '', $other['label']) }}
                                                    </span>
                                                    @include('admin.user.partials.override-buttons', ['perm' => $other])
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="text-center py-2">
                                                <span class="text-muted opacity-25 small">—</span>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function () {
    // Search functionality
    $('#permSearch').on('keyup', function() {
        let value = $(this).val().toLowerCase();
        $(".permission-grid tbody tr").filter(function() {
            $(this).toggle($(this).find('.module-name').text().toLowerCase().indexOf(value) > -1)
        });
    });

    // Live-update "Effective" indicator color/icon based on selection
    $('input[type=radio]').on('change', function () {
        const parent = $(this).closest('.override-group');
        const roleHas = parent.data('role') === 1;
        const val = $(this).val();
        
        let effectiveStatus;
        if (val === 'grant') effectiveStatus = true;
        else if (val === 'revoke') effectiveStatus = false;
        else effectiveStatus = roleHas;

        const indicator = parent.find('.effective-indicator');
        if (effectiveStatus) {
            indicator.html('<i class="fas fa-check-circle text-success" title="Allowed"></i>');
        } else {
            indicator.html('<i class="fas fa-ban text-danger" title="Denied"></i>');
        }
    });

    // Submit via AJAX
    $('#permissionForm').submit(function (e) {
        e.preventDefault();

        const url = "{{ route('admin.user.permissions.update', $user->id) }}";
        const data = $(this).serialize();
        const saveBtn = $('#saveBtn');
        const originalText = saveBtn.html();

        saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: url,
            method: 'POST',
            data: data + '&_method=PUT',
            success: function (res) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: res.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function (xhr) {
                Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Failed to save.' });
            },
            complete: function () {
                saveBtn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
@endsection
