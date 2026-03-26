{{-- Device Edit Modal - include in pages where device edit is needed --}}
<div class="modal fade" id="deviceEditModal" tabindex="-1" aria-labelledby="deviceEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form id="device-edit-form">
                @csrf
                @method('PUT')
                <input type="hidden" id="device_edit_id" name="device_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="deviceEditModalLabel">Edit Device</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="modal_product_id" class="form-label">Product <span class="text-danger">*</span></label>
                                <select class="form-control" id="modal_product_id" name="product_id" required>
                                    <option value="">Select Product</option>
                                    {{-- Options populated via JS --}}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="modal_imei" class="form-label">IMEI <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modal_imei" name="imei" placeholder="Enter IMEI" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="modal_ram" class="form-label">RAM <span class="text-danger">*</span></label>
                                <select class="form-control" id="modal_ram" name="ram_option_id" required>
                                    <option value="">Select RAM</option>
                                    @foreach(($ramOptions ?? []) as $opt)
                                        <option value="{{ $opt->id }}">{{ $opt->value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="modal_storage" class="form-label">Storage <span class="text-danger">*</span></label>
                                <select class="form-control" id="modal_storage" name="storage_option_id" required>
                                    <option value="">Select Storage</option>
                                    @foreach(($storageOptions ?? []) as $opt)
                                        <option value="{{ $opt->id }}">{{ $opt->value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Color <span class="text-danger">*</span></label>
                                <div id="modal-color-selection-wrapper">
                                    <div class="d-flex gap-2 mb-1" id="modal-existing-color-group">
                                        <select class="form-control" id="modal_color_option_id" name="color_option_id">
                                            <option value="">Select Color</option>
                                            @foreach (($colorOptions ?? []) as $opt)
                                                <option value="{{ $opt->id }}">{{ $opt->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-sm btn-outline-primary whitespace-nowrap" id="modal-btn-toggle-new-color" title="Add New Color">
                                            <i class="fa fa-plus"></i> New
                                        </button>
                                    </div>
                                    <div class="d-none" id="modal-new-color-group">
                                        <div class="d-flex gap-2">
                                            <input type="text" class="form-control" id="modal_new_color_name" name="new_color_name" placeholder="Color Name">
                                            <input type="color" class="form-control form-control-color" id="modal_new_color_value" name="new_color_value" value="#000000" title="Choose a color" style="width: 60px; height: 38px;">
                                            <button type="button" class="btn btn-outline-secondary" id="modal-btn-cancel-new-color" title="Cancel New Color">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="modal_battery_percentage" class="form-label">Battery % <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="modal_battery_percentage" name="battery_percentage" min="0" max="100" placeholder="0-100" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="modal_condition_grade" class="form-label">Condition Grade <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modal_condition_grade" name="condition_grade" placeholder="e.g. A, B, C" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="modal_status" class="form-label">Status</label>
                                <select class="form-control" id="modal_status" name="status">
                                    <option value="available">Available</option>
                                    <option value="sold">Sold</option>
                                    <option value="reserved">Reserved</option>
                                    <option value="defective">Defective</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="modal_purchase_price" class="form-label">Purchase Price</label>
                                <input type="number" step="0.01" class="form-control" id="modal_purchase_price" name="purchase_price" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="modal_selling_price" class="form-label">Selling Price</label>
                                <input type="number" step="0.01" class="form-control" id="modal_selling_price" name="selling_price" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="modal_image_input" class="form-label">Images</label>
                        <input type="file" class="form-control" id="modal_image_input" accept="image/*" multiple>
                        <div class="mt-2" id="modal_image_preview_wrapper"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
