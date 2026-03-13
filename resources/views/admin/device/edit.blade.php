@extends('layouts.app')

@section('meta')
    <meta name="description" content="Edit Device">
    <meta name="keywords" content="Device, Edit, Flexicell">
@endsection

@section('title', 'Edit Device')

@section('content')
    <div class="d-flex justify-content-center mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h1 class="card-title h4 d-flex align-items-center gap-2 mb-4">
                        Edit Device
                    </h1>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="edit-device-form" action="{{ route('admin.device.update', $device->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="product_id" class="form-label">Product <span class="text-danger fs-5">*</span></label>
                                    <select class="form-control" id="product_id" name="product_id" required>
                                        <option value="">Select Product</option>
                                        @foreach ($products as $p)
                                            <option value="{{ $p->id }}" {{ old('product_id', $device->product_id) == $p->id ? 'selected' : '' }}>
                                                {{ $p->phoneModel->model_name ?? '-' }} ({{ $p->product_type }}) - {{ $p->phoneModel->brand->brand_name ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="imei" class="form-label">IMEI <span class="text-danger fs-5">*</span></label>
                                    <input type="text" class="form-control" id="imei" name="imei"
                                        placeholder="Enter IMEI number" value="{{ old('imei', $device->imei) }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="ram" class="form-label">RAM <span class="text-danger fs-5">*</span></label>
                                    <input type="text" class="form-control" id="ram" name="ram"
                                        placeholder="e.g. 8GB" value="{{ old('ram', $device->ram) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="storage" class="form-label">Storage <span class="text-danger fs-5">*</span></label>
                                    <input type="text" class="form-control" id="storage" name="storage"
                                        placeholder="e.g. 128GB" value="{{ old('storage', $device->storage) }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="color" class="form-label">Color <span class="text-danger fs-5">*</span></label>
                                    <input type="text" class="form-control" id="color" name="color"
                                        placeholder="e.g. Black" value="{{ old('color', $device->color) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="battery_percentage" class="form-label">Battery % <span class="text-danger fs-5">*</span></label>
                                    <input type="number" class="form-control" id="battery_percentage" name="battery_percentage"
                                        placeholder="0-100" value="{{ old('battery_percentage', $device->battery_percentage) }}" min="0" max="100" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="condition_grade" class="form-label">Condition Grade <span class="text-danger fs-5">*</span></label>
                                    <input type="text" class="form-control" id="condition_grade" name="condition_grade"
                                        placeholder="e.g. A, B, C" value="{{ old('condition_grade', $device->condition_grade) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="available" {{ old('status', $device->status) == 'available' ? 'selected' : '' }}>Available</option>
                                        <option value="sold" {{ old('status', $device->status) == 'sold' ? 'selected' : '' }}>Sold</option>
                                        <option value="reserved" {{ old('status', $device->status) == 'reserved' ? 'selected' : '' }}>Reserved</option>
                                        <option value="defective" {{ old('status', $device->status) == 'defective' ? 'selected' : '' }}>Defective</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Update Device</button>
                        <a href="{{ route('admin.device.index') }}" class="btn btn-secondary mt-3">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    $(document).ready(function () {
        $('#edit-device-form').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    Swal.fire({ icon: 'success', title: 'Success', text: response.message }).then(() => {
                        window.location.href = "{{ route('admin.device.index') }}";
                    });
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON?.errors;
                    let msg = xhr.responseJSON?.message || 'Something went wrong';
                    if (errors) {
                        msg = Object.values(errors).flat().join('<br>');
                    }
                    Swal.fire({ icon: 'error', title: 'Error', html: msg });
                }
            });
        });
    });
</script>
@endsection
