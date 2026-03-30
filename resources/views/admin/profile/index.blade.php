@extends('layouts.app')

@section('title', 'My Profile')

@section('style')
<style>
    .profile-banner {
        height: 160px;
        background: linear-gradient(135deg, #5D87FF 0%, #49BEFF 100%);
        border-radius: 8px 8px 0 0;
        position: relative;
    }
    .profile-avatar-wrapper {
        position: relative;
        margin-top: -60px;
        display: inline-block;
    }
    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 2px 12px rgba(0,0,0,0.15);
        background: #e2e8f0;
    }
    .avatar-overlay {
        position: absolute;
        bottom: 4px;
        right: 4px;
        width: 34px;
        height: 34px;
        background: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 1px 4px rgba(0,0,0,0.2);
        cursor: pointer;
        transition: background 0.2s;
    }
    .avatar-overlay:hover { background: #f0f4f8; }
    .profile-info-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; font-weight: 600; margin-bottom: 2px; }
    .profile-info-value { font-size: 0.95rem; font-weight: 500; color: #334155; }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
            @if($user->assignedRole)
                <a href="{{ route('admin.role.edit', $user->assignedRole->id) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-shield-alt me-1"></i> Permissions
                </a>
            @endif
        </div>

        {{-- Profile Card --}}
        <div class="card mb-4 overflow-hidden">
            <div class="profile-banner"></div>
            <div class="card-body pt-0">
                <div class="d-flex flex-column flex-md-row align-items-center align-items-md-end gap-3" style="margin-top: -30px;">
                    <div class="profile-avatar-wrapper">
                        @if($user->profile_photo)
                            <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="{{ $user->name }}" class="profile-avatar" id="avatarPreview">
                        @else
                            <div class="profile-avatar d-flex align-items-center justify-content-center" id="avatarPreview" style="font-size: 2.5rem; color: #94a3b8;">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                        <label class="avatar-overlay" for="photoInput" title="Change photo">
                            <i class="fas fa-camera text-primary" style="font-size: 14px;"></i>
                        </label>
                    </div>
                    <div class="text-center text-md-start pb-2">
                        <h4 class="mb-0 fw-bold">{{ $user->name }}</h4>
                        <span class="badge bg-primary mt-1">{{ $user->assignedRole?->name ?? 'N/A' }}</span>
                        <div class="text-muted small mt-1">Member since {{ $user->created_at->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hidden photo upload form --}}
        <form id="photoForm" action="{{ route('admin.profile.photo') }}" method="POST" enctype="multipart/form-data" class="d-none">
            @csrf
            <input type="file" id="photoInput" name="profile_photo" accept="image/jpeg,image/png,image/webp" onchange="document.getElementById('photoForm').submit();">
        </form>

        <div class="row g-4">
            {{-- Profile Info --}}
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="mb-0 fw-bold">Profile Information</h5>
                            <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#editProfileForm">
                                <i class="fas fa-pen me-1"></i> Edit
                            </button>
                        </div>

                        {{-- View mode --}}
                        <div class="row g-3" id="profileView">
                            <div class="col-6">
                                <div class="profile-info-label">Full Name</div>
                                <div class="profile-info-value">{{ $user->name }}</div>
                            </div>
                            <div class="col-6">
                                <div class="profile-info-label">Email</div>
                                <div class="profile-info-value">{{ $user->email }}</div>
                            </div>
                            <div class="col-6">
                                <div class="profile-info-label">Phone</div>
                                <div class="profile-info-value">{{ $user->phone ?? '-' }}</div>
                            </div>
                            <div class="col-6">
                                <div class="profile-info-label">Role</div>
                                <div class="profile-info-value">{{ $user->assignedRole?->name ?? '-' }}</div>
                            </div>
                            <div class="col-12">
                                <div class="profile-info-label">Address</div>
                                <div class="profile-info-value">{{ $user->address ?? '-' }}</div>
                            </div>
                        </div>

                        {{-- Edit mode (collapsible) --}}
                        <div class="collapse mt-3" id="editProfileForm">
                            <form action="{{ route('admin.profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Phone</label>
                                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Address</label>
                                        <input type="text" name="address" class="form-control" value="{{ old('address', $user->address) }}">
                                    </div>
                                    <div class="col-12 d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#editProfileForm">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Change Password --}}
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="mb-4 fw-bold">Change Password</h5>
                        <form action="{{ route('admin.profile.password') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label class="form-label">Current Password</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="password" class="form-control" required>
                                <small class="text-muted">Minimum 8 characters.</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Remove photo --}}
        @if($user->profile_photo)
        <div class="mt-3">
            <form action="{{ route('admin.profile.photo.remove') }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove profile photo?');">
                    <i class="fas fa-trash me-1"></i> Remove Profile Photo
                </button>
            </form>
        </div>
        @endif

    </div>
</div>
@endsection
