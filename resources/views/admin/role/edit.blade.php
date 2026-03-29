@extends('layouts.app')

@section('title', 'Edit Role — ' . $role->name)

@section('style')
<style>
    .permission-grid thead th {
        background-color: #f8f9fa;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        text-align: center;
        border-bottom: 2px solid #dee2e6;
        vertical-align: middle;
    }
    .permission-grid tbody td {
        vertical-align: middle;
    }
    .module-name {
        font-weight: 600;
        color: #495057;
        font-size: 0.875rem;
    }
    .action-check-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .form-check-input {
        cursor: pointer;
        width: 1.1rem;
        height: 1.1rem;
    }
    .custom-badge {
        font-size: 0.65rem;
        padding: 0.3em 0.6em;
        border-radius: 4px; /* Changed to slightly rounded for a more modern look matching Bootstrap buttons */
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid #dee2e6;
        background-color: #f8f9fa;
        color: #6c757d;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        margin: 1px;
        white-space: nowrap;
    }
    .custom-badge.active {
        background-color: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
        box-shadow: 0 2px 4px rgba(13, 110, 253, 0.2);
    }
    .custom-badge:hover:not(.active) {
        background-color: #e9ecef;
    }
    .sticky-header {
        position: sticky;
        top: 0;
        z-index: 100;
        background: white;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <form id="editRoleForm">
            @csrf
            @method('PUT')
            
            <div class="card mb-4">
                <div class="card-body">
                    {{-- Header Section --}}
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-2 bg-light rounded text-primary">
                                <i class="fas fa-shield-halved fa-lg"></i>
                            </div>
                            <div>
                                <h1 class="h5 mb-1">Edit Access Level</h1>
                                <p class="text-muted small mb-0">Configuring permissions for <span class="fw-bold text-dark">{{ $role->name }}</span></p>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.role.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                                <i class="fas fa-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm" id="saveBtn">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </div>
                    </div>

                    {{-- Role Information --}}
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Display Name</label>
                            <input type="text" name="name" value="{{ $role->name }}" class="form-control form-control-sm h-auto py-2" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">System Code</label>
                            <input type="text" value="{{ $role->code }}" class="form-control form-control-sm h-auto py-2 bg-light" disabled>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Permissions Table --}}
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="position-relative" style="max-width: 300px; width: 100%;">
                        <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" id="permSearch" class="form-control form-control-sm ps-5" placeholder="Search modules...">
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-xs text-muted fw-bold text-uppercase me-2" style="font-size: 0.65rem; letter-spacing: 1px;">Quick Actions:</span>
                        <div class="btn-group btn-group-sm">
                            <button type="button" id="selectAll" class="btn btn-outline-secondary">Select All</button>
                            <button type="button" id="deselectAll" class="btn btn-outline-secondary">Reset</button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle permission-grid mb-0" id="permissionGrid">
                        <thead>
                            <tr>
                                <th class="text-start" style="width: 250px;">Module / Section</th>
                                @foreach(['view' => 'View', 'create' => 'Create', 'edit' => 'Update', 'delete' => 'Delete'] as $key => $label)
                                <th style="width: 120px;">
                                    <div class="d-flex flex-column align-items-center gap-1 cursor-pointer" onclick="toggleColumn('{{ $key }}')">
                                        <span class="small">{{ $label }}</span>
                                        <input type="checkbox" class="form-check-input col-toggle" data-action="{{ $key }}">
                                    </div>
                                </th>
                                @endforeach
                                <th class="text-start">Other Permissions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupedPermissions as $module => $data)
                            <tr class="module-row">
                                <td class="px-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="checkbox" class="form-check-input row-toggle">
                                        <span class="module-name">{{ $data['displayName'] }}</span>
                                    </div>
                                </td>

                                @foreach(['view', 'create', 'edit', 'delete'] as $action)
                                <td class="text-center">
                                    @if($data[$action])
                                        <div class="action-check-wrapper">
                                            <input type="checkbox" 
                                                name="permissions[]" 
                                                value="{{ $data[$action]['name'] }}"
                                                data-action="{{ $action }}"
                                                class="form-check-input perm-check"
                                                {{ $data[$action]['selected'] ? 'checked' : '' }}>
                                        </div>
                                    @else
                                        <span class="text-muted opacity-25 small">—</span>
                                    @endif
                                </td>
                                @endforeach

                                <td class="px-3 py-2">
                                    @if(!empty($data['other']))
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($data['other'] as $other)
                                                <div class="position-relative">
                                                    <input class="d-none perm-check" 
                                                        type="checkbox" 
                                                        name="permissions[]" 
                                                        value="{{ $other['name'] }}"
                                                        id="perm_{{ $other['id'] }}"
                                                        {{ $other['selected'] ? 'checked' : '' }}>
                                                    <label for="perm_{{ $other['id'] }}" class="custom-badge {{ $other['selected'] ? 'active' : '' }}">
                                                        <i class="fas {{ $other['selected'] ? 'fa-check' : 'fa-plus' }}"></i>
                                                        {{ str_replace($module.'.', '', $other['label'] ?: $other['name']) }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted opacity-25 small">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function () {
    const grid = $('#permissionGrid');

    // Handle Search
    $('#permSearch').on('keyup', function() {
        const query = $(this).val().toLowerCase();
        $('.module-row').each(function() {
            const label = $(this).find('.module-name').text().toLowerCase();
            $(this).toggle(label.includes(query));
        });
    });

    // Badge Click Logic
    $('.custom-badge').click(function(e) {
        const checkbox = $('#' + $(this).attr('for'));
        // The label's default behavior will toggle the checkbox, but we need to update our class
        setTimeout(() => {
            if (checkbox.is(':checked')) {
                $(this).addClass('active').find('i').removeClass('fa-plus').addClass('fa-check');
            } else {
                $(this).removeClass('active').find('i').removeClass('fa-check').addClass('fa-plus');
            }
        }, 10);
    });

    // Row Toggle
    $('.row-toggle').on('change', function() {
        const isChecked = $(this).is(':checked');
        const row = $(this).closest('tr');
        row.find('.perm-check').each(function() {
            $(this).prop('checked', isChecked).trigger('change');
            // If it's a hidden checkbox for a badge, trigger the label update
            if ($(this).hasClass('d-none')) {
                const label = $('label[for="' + $(this).attr('id') + '"]');
                if (isChecked) {
                    label.addClass('active').find('i').removeClass('fa-plus').addClass('fa-check');
                } else {
                    label.removeClass('active').find('i').removeClass('fa-check').addClass('fa-plus');
                }
            }
        });
    });

    // Column Toggle Function (Global helper)
    window.toggleColumn = function(action) {
        const colHeaderCheck = $('.col-toggle[data-action="' + action + '"]');
        const newState = !colHeaderCheck.is(':checked');
        colHeaderCheck.prop('checked', newState);
        $('.perm-check[data-action="' + action + '"]').prop('checked', newState).trigger('change');
    };

    // Global Select All
    $('#selectAll').click(function() {
        $('.perm-check, .row-toggle, .col-toggle').prop('checked', true);
        $('.custom-badge').addClass('active').find('i').removeClass('fa-plus').addClass('fa-check');
    });

    $('#deselectAll').click(function() {
        $('.perm-check, .row-toggle, .col-toggle').prop('checked', false);
        $('.custom-badge').removeClass('active').find('i').removeClass('fa-check').addClass('fa-plus');
    });

    // AJAX Submission
    $('#editRoleForm').submit(function (e) {
        e.preventDefault();
        
        const saveBtn = $('#saveBtn');
        const originalContent = saveBtn.html();
        const formData = $(this).serialize();

        saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: "{{ route('admin.role.update', $role->id) }}",
            method: "POST",
            data: formData,
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
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Failed to update role.'
                });
            },
            complete: function () {
                saveBtn.prop('disabled', false).html(originalContent);
            }
        });
    });
});
</script>
@endsection
