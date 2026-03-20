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
                                <input type="text" class="form-control" id="modal_ram" name="ram" placeholder="e.g. 8GB" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="modal_storage" class="form-label">Storage <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modal_storage" name="storage" placeholder="e.g. 128GB" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="modal_color" class="form-label">Color <span class="text-danger">*</span></label>
                                <input type="color" class="form-control form-control-color w-100" id="modal_color" name="color" value="#000000" required>
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
