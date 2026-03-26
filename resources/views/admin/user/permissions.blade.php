@extends('layouts.app')

@section('title', 'Manage Permissions — ' . $user->name)

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
                    <a href="{{ route('admin.user.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                </div>

                {{-- Legend --}}
                <div class="alert alert-info d-flex gap-3 align-items-center py-2 mb-4">
                    <i class="fas fa-info-circle"></i>
                    <div class="small">
                        <strong>Inherit</strong> = use the role default &nbsp;|&nbsp;
                        <strong class="text-success">Grant</strong> = force allow (even if role can't) &nbsp;|&nbsp;
                        <strong class="text-danger">Revoke</strong> = force deny (even if role can)
                    </div>
                </div>

                {{-- Permission Table --}}
                <form id="permissionForm">
                    @csrf
                    <table class="table table-hover table-sm align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Permission</th>
                                <th class="text-center">Role Default</th>
                                <th class="text-center" style="min-width:260px">Override</th>
                                <th class="text-center">Effective</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permissions as $perm)
                            <tr>
                                <td>
                                    <div class="fw-semibold small">{{ $perm['label'] }}</div>
                                    <div class="text-muted" style="font-size:11px">{{ $perm['name'] }}</div>
                                </td>
                                <td class="text-center">
                                    @if($perm['role_has'])
                                        <span class="badge bg-success"><i class="fas fa-check"></i> Yes</span>
                                    @else
                                        <span class="badge bg-light text-dark border"><i class="fas fa-times"></i> No</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <input type="radio"
                                            class="btn-check"
                                            name="permissions[{{ $perm['id'] }}]"
                                            id="inherit_{{ $perm['id'] }}"
                                            value="inherit"
                                            {{ $perm['override'] === null ? 'checked' : '' }}>
                                        <label class="btn btn-outline-secondary" for="inherit_{{ $perm['id'] }}">Inherit</label>

                                        <input type="radio"
                                            class="btn-check"
                                            name="permissions[{{ $perm['id'] }}]"
                                            id="grant_{{ $perm['id'] }}"
                                            value="grant"
                                            {{ $perm['override'] === 'grant' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-success" for="grant_{{ $perm['id'] }}">Grant</label>

                                        <input type="radio"
                                            class="btn-check"
                                            name="permissions[{{ $perm['id'] }}]"
                                            id="revoke_{{ $perm['id'] }}"
                                            value="revoke"
                                            {{ $perm['override'] === 'revoke' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-danger" for="revoke_{{ $perm['id'] }}">Revoke</label>
                                    </div>
                                </td>
                                <td class="text-center effective-cell" data-id="{{ $perm['id'] }}" data-role="{{ $perm['role_has'] ? '1' : '0' }}">
                                    @if($perm['effective'])
                                        <span class="badge bg-success"><i class="fas fa-check-circle"></i> Allowed</span>
                                    @else
                                        <span class="badge bg-danger"><i class="fas fa-ban"></i> Denied</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary px-4" id="saveBtn">
                            <i class="fas fa-save"></i> Save Permissions
                        </button>
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

    // Live-update the "Effective" column as radio buttons change
    $('input[type=radio]').on('change', function () {
        const permId  = $(this).attr('name').match(/\d+/)[0];
        const val     = $(this).val();
        const cell    = $(`.effective-cell[data-id="${permId}"]`);
        const roleHas = cell.data('role') === 1 || cell.data('role') === '1';

        let effective;
        if (val === 'grant')   effective = true;
        else if (val === 'revoke') effective = false;
        else effective = roleHas;

        cell.html(effective
            ? '<span class="badge bg-success"><i class="fas fa-check-circle"></i> Allowed</span>'
            : '<span class="badge bg-danger"><i class="fas fa-ban"></i> Denied</span>'
        );
    });

    // Submit via AJAX
    $('#permissionForm').submit(function (e) {
        e.preventDefault();

        const url  = "{{ route('admin.user.permissions.update', $user->id) }}";
        const data = $(this).serialize();

        $('#saveBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

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
                    showConfirmButton: false,
                    timerProgressBar: true,
                });
            },
            error: function (xhr) {
                Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Failed to save.' });
            },
            complete: function () {
                $('#saveBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Save Permissions');
            }
        });
    });
});
</script>
@endsection
