@extends('layouts.app')

@section('title', $user->name . ' - Profile')

@section('style')
<style>
    .profile-banner {
        height: 140px;
        background: linear-gradient(135deg, #5D87FF 0%, #49BEFF 100%);
        border-radius: 8px 8px 0 0;
    }
    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 2px 12px rgba(0,0,0,0.15);
        background: #e2e8f0;
    }
    .info-label { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; font-weight: 600; margin-bottom: 2px; }
    .info-value { font-size: 0.95rem; font-weight: 500; color: #334155; }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">

        <div class="mb-3">
            <a href="{{ route('admin.user.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Users
            </a>
        </div>

        {{-- Profile Header --}}
        <div class="card mb-4 overflow-hidden">
            <div class="profile-banner"></div>
            <div class="card-body pt-0">
                <div class="d-flex flex-column flex-md-row align-items-center align-items-md-end gap-3" style="margin-top: -50px;">
                    @if($user->profile_photo)
                        <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="{{ $user->name }}" class="profile-avatar">
                    @else
                        <div class="profile-avatar d-flex align-items-center justify-content-center" style="font-size: 2.2rem; color: #94a3b8;">
                            <i class="fas fa-user"></i>
                        </div>
                    @endif
                    <div class="text-center text-md-start pb-2">
                        <h4 class="mb-0 fw-bold">{{ $user->name }}</h4>
                        <span class="badge bg-primary mt-1">{{ $user->assignedRole?->name ?? 'N/A' }}</span>
                        <span class="badge bg-{{ $user->status ? 'success' : 'danger' }} mt-1">{{ $user->status ? 'Active' : 'Inactive' }}</span>
                        <div class="text-muted small mt-1">Joined {{ $user->created_at->format('M d, Y') }}</div>
                    </div>
                    <div class="ms-md-auto pb-2 d-flex gap-2">
                        <a href="#" class="btn btn-sm btn-primary edit-user-btn"
                            data-id="{{ $user->id }}"
                            data-name="{{ $user->name }}"
                            data-email="{{ $user->email }}"
                            data-phone="{{ $user->phone }}"
                            data-address="{{ $user->address }}"
                            data-role="{{ $user->assignedRole?->code }}"
                            data-bs-toggle="modal" data-bs-target="#editModal">
                            <i class="fas fa-pen me-1"></i> Edit
                        </a>
                        @if($user->assignedRole)
                            <a href="{{ route('admin.role.edit', $user->assignedRole->id) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-shield-alt me-1"></i> Permissions
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Cards --}}
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Personal Information</h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="info-label">Full Name</div>
                                <div class="info-value">{{ $user->name }}</div>
                            </div>
                            <div class="col-6">
                                <div class="info-label">Username</div>
                                <div class="info-value">{{ $user->username ?? '-' }}</div>
                            </div>
                            <div class="col-6">
                                <div class="info-label">Email</div>
                                <div class="info-value">{{ $user->email }}</div>
                            </div>
                            <div class="col-6">
                                <div class="info-label">Phone</div>
                                <div class="info-value">{{ $user->phone ?? '-' }}</div>
                            </div>
                            <div class="col-6">
                                <div class="info-label">NRC</div>
                                <div class="info-value">{{ $user->nrc ?? '-' }}</div>
                            </div>
                            <div class="col-6">
                                <div class="info-label">Role</div>
                                <div class="info-value">{{ $user->assignedRole?->name ?? '-' }}</div>
                            </div>
                            <div class="col-12">
                                <div class="info-label">Address</div>
                                <div class="info-value">{{ $user->address ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Account Details</h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="info-label">Status</div>
                                <div class="info-value">
                                    <span class="badge bg-{{ $user->status ? 'success' : 'danger' }}">{{ $user->status ? 'Active' : 'Inactive' }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-label">Primary Account</div>
                                <div class="info-value">
                                    @if($user->is_primary_account)
                                        <span class="badge bg-info">Yes</span>
                                    @else
                                        <span class="text-muted">No</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-label">Email Verified</div>
                                <div class="info-value">
                                    @if($user->email_verified_at)
                                        <span class="text-success"><i class="fas fa-check-circle me-1"></i>{{ $user->email_verified_at->format('M d, Y') }}</span>
                                    @else
                                        <span class="text-muted">Not verified</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-label">Created At</div>
                                <div class="info-value">{{ $user->created_at->format('M d, Y h:i A') }}</div>
                            </div>
                            <div class="col-6">
                                <div class="info-label">Updated At</div>
                                <div class="info-value">{{ $user->updated_at->format('M d, Y h:i A') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Edit Modal (reuse from index) --}}
<form method="POST" id="editForm" action="{{ route('admin.user.update', $user->id) }}">
    @csrf
    @method('PUT')
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" value="{{ $user->name }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" value="{{ $user->email }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password <small class="text-muted">(leave blank to keep current)</small></label>
                        <input type="password" class="form-control" id="edit_password" name="password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" id="edit_phone" name="phone" value="{{ $user->phone }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <input type="text" class="form-control" id="edit_address" name="address" value="{{ $user->address }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-control" id="edit_role" name="role">
                            <option value="superadmin" {{ $user->assignedRole?->code === 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                            <option value="manager" {{ $user->assignedRole?->code === 'manager' ? 'selected' : '' }}>Manager</option>
                            <option value="staff" {{ $user->assignedRole?->code === 'staff' ? 'selected' : '' }}>Staff</option>
                        </select>
                    </div>
                    <input type="hidden" id="edit_id" name="id" value="{{ $user->id }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('script')
<script>
    $(function() {
        $('#editForm').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                processData: false,
                success: function(res) {
                    $('#editModal').modal('hide');
                    Swal.fire({
                        toast: true, position: 'top-end', icon: 'success',
                        title: res.message ?? 'User updated successfully',
                        timer: 2000, showConfirmButton: false
                    });
                    setTimeout(() => location.reload(), 1500);
                },
                error: function(xhr) {
                    Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Something went wrong' });
                }
            });
        });
    });
</script>
@endsection
