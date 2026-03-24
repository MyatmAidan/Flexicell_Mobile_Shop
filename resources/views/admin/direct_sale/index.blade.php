@extends('layouts.app')

@section('meta')
    <meta name="description" content="Direct Sale (POS)">
    <meta name="keywords" content="Direct Sale, POS, Flexicell">
@endsection

@section('title', 'Direct Sale')

@section('style')
<style>
    .pos-panel { min-height: 70vh; }
    .pos-scroll { max-height: 60vh; overflow: auto; }
    .pos-product-item { cursor: pointer; }
    .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
</style>
@endsection

@section('content')
    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <div class="card pos-panel">
                <div class="card-body">
                    <h5 class="mb-3">Direct Sale For All Products</h5>

                    <div class="row mb-3">
    
                        <div class="col-md-6">
                            <label class="form-label">Search product</label>
                            <input type="text" id="productSearch" class="form-control" placeholder="Search by model name or type...">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Quick IMEI</label>
                            <div class="input-group">
                                <input type="text" id="imeiInput" class="form-control mono" placeholder="Scan / type IMEI">
                                <button class="btn btn-primary" id="imeiAddBtn" type="button">Add</button>
                            </div>
                            <small class="text-muted">IMEI items are sold as specific devices.</small>
                        </div>

                    </div>

                    <div class="pos-scroll border rounded p-2" id="productResults">
                        <div class="text-muted small">Type to search products…</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">Shopping Cart</h5>
                        <button type="button" class="btn btn-md btn-outline-danger" id="clearCartBtn">Clear</button>
                    </div>

                    <div class="table-responsive pos-scroll">
                        <table class="table table-sm table-hover align-middle" id="cartTable">
                            <thead>
                                <tr>
                                    <th style="width: 34%">Product</th>
                                    <th style="width: 14%">Type</th>
                                    <th style="width: 12%">Qty</th>
                                    <th style="width: 14%">Price</th>
                                    <th style="width: 14%">Disc</th>
                                    <th style="width: 12%">Total</th>
                                    <th style="width: 1%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td colspan="7" class="text-muted small">Cart is empty.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Customer &amp; Payment</h5>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Customer Name</label>
                            <input type="text" id="customerName" class="form-control" placeholder="Required" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Customer Phone</label>
                            <input type="text" id="customerPhone" class="form-control" placeholder="Optional">
                        </div>
                    </div>
                    <div id="customerExtendedInfo" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label">Customer NRC</label>
                            <input type="text" id="customerNRC" class="form-control" placeholder="Required for installments">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Customer Address</label>
                            <textarea id="customerAddress" class="form-control" rows="2" placeholder="Optional"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Attachments (NRC, Documents...)</label>
                            <input type="file" id="customerAttachments" class="form-control" multiple>
                            <small class="text-muted">Max 5MB per file. Formats: jpg, png, pdf.</small>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label d-block">Payment Type</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="paymentType" id="payCash" value="cash" checked>
                            <label class="form-check-label" for="payCash">Cash</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="paymentType" id="payInstallment" value="installment">
                            <label class="form-check-label" for="payInstallment">Installment</label>
                        </div>
                    </div>

                    <div class="border rounded p-2 mb-3" id="installmentBox" style="display:none;">
                        <div class="mb-2">
                            <label class="form-label">Plan (months / rate%)</label>
                            <select class="form-control" id="installmentRate">
                                <option value="">Select plan</option>
                                @foreach ($installmentRates as $r)
                                    <option value="{{ $r->id }}" data-months="{{ $r->month_option }}" data-rate="{{ $r->rate }}">
                                        {{ $r->month_option }} months ({{ $r->rate }}%)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Down Payment</label>
                            <input type="number" class="form-control" id="downPayment" value="0" min="0" step="0.01">
                        </div>
                        <div class="small text-muted">
                            Total w/ interest: <span class="fw-semibold" id="totalWithInterest">0.00</span><br>
                            Monthly: <span class="fw-semibold" id="monthlyAmount">0.00</span>
                        </div>
                    </div>

                    <div class="border rounded p-2 mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-semibold" id="subTotal">0.00</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Discount</span>
                            <span class="fw-semibold" id="discTotal">0.00</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Grand Total</span>
                            <span class="fw-bold" id="grandTotal">0.00</span>
                        </div>
                    </div>

                    <button class="btn btn-success w-100" id="checkoutBtn" type="button">Checkout</button>
                </div>
            </div>
            
        </div>
    </div>
@endsection

@section('script')
<script>
    $(function() {
        const csrf = $('meta[name="csrf-token"]').attr('content');
        const urls = {
            products: "{{ route('admin.direct_sale.products') }}",
            devices: "{{ route('admin.direct_sale.devices') }}",
            checkout: "{{ route('admin.direct_sale.checkout') }}"
        };
        const productImageBase = "{{ asset('storage/products') }}";

        /** Cart line:
         * {
         *   key: string,
         *   product_id: number,
         *   product_label: string,
         *   product_type: 'new'|'second hand'|string,
         *   quantity: number,
         *   unit_price: number,
         *   discount_price: number,
         *   device_id?: number,
         *   imei?: string
         * }
         */
        let cart = [];
        let productCache = {};

        function money(n) {
            return (Number(n || 0)).toFixed(2);
        }

        function calcTotals() {
            let sub = 0, disc = 0;
            cart.forEach(l => {
                sub += Number(l.unit_price) * Number(l.quantity);
                disc += Number(l.discount_price) * Number(l.quantity);
            });
            const grand = Math.max(0, sub - disc);
            $('#subTotal').text(money(sub));
            $('#discTotal').text(money(disc));
            $('#grandTotal').text(money(grand));
            updateInstallmentPreview(grand);
            return { sub, disc, grand };
        }

        function renderCart() {
            const $tbody = $('#cartTable tbody');
            $tbody.empty();
            if (cart.length === 0) {
                $tbody.append('<tr><td colspan="7" class="text-muted small">Cart is empty.</td></tr>');
                calcTotals();
                return;
            }

            cart.forEach((l, idx) => {
                const lineTotal = (Number(l.unit_price) - Number(l.discount_price)) * Number(l.quantity);
                const qtyInput = l.device_id
                    ? `<span class="text-muted">1</span>`
                    : `<input type="number" class="form-control form-control-sm qty-input" data-idx="${idx}" min="1" value="${l.quantity}">`;

                $tbody.append(`
                    <tr>
                        <td>
                            <div class="fw-semibold">${l.product_label}</div>
                            ${l.device_id ? `<div class="small text-muted mono">IMEI: ${l.imei || '-'}</div>` : ''}
                        </td>
                        <td><span class="badge bg-${l.device_id ? 'info' : 'secondary'}">${l.device_id ? 'IMEI' : 'QTY'}</span></td>
                        <td>${qtyInput}</td>
                        <td><input type="number" class="form-control form-control-sm price-input" data-idx="${idx}" min="0" step="0.01" value="${l.unit_price}"></td>
                        <td><input type="number" class="form-control form-control-sm disc-input" data-idx="${idx}" min="0" step="0.01" value="${l.discount_price}"></td>
                        <td class="fw-semibold">${money(lineTotal)}</td>
                        <td><button class="btn btn-md btn-outline-danger remove-line-btn" data-idx="${idx}"><i class="fa fa-trash"></i></button></td>
                    </tr>
                `);
            });
            calcTotals();
        }

        function upsertQtyProduct(product, quantity) {
            const key = `p-${product.id}`;
            const existing = cart.find(x => x.key === key);
            if (existing) {
                existing.quantity += quantity;
            } else {
                cart.push({
                    key,
                    product_id: product.id,
                    product_label: `${product.label} - ${product.brand}`,
                    product_type: product.product_type,
                    quantity,
                    unit_price: Number(product.selling_price || 0),
                    discount_price: 0,
                });
            }
            renderCart();
        }

        function addImeiDevice(deviceData) {
            const key = `d-${deviceData.device_id}`;
            if (cart.some(x => x.key === key)) {
                Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: 'Already in cart', timer: 1500, showConfirmButton: false });
                return;
            }
            cart.push({
                key,
                product_id: deviceData.product_id,
                product_label: `${deviceData.product_label} - ${deviceData.brand}`,
                product_type: 'second hand',
                quantity: 1,
                unit_price: Number(deviceData.selling_price || 0),
                discount_price: 0,
                device_id: deviceData.device_id,
                imei: deviceData.imei
            });
            renderCart();
        }

        // Product search
        let searchTimer;
        $('#productSearch').on('input', function() {
            clearTimeout(searchTimer);
            const q = $(this).val();
            searchTimer = setTimeout(() => loadProducts(q), 250);
        });

        function loadProducts(q) {
            $('#productResults').html('<div class="text-muted small">Loading…</div>');
            $.get(urls.products, { q })
                .done(function(res) {
                    const items = res.data || [];
                    productCache = {};
                    items.forEach(p => productCache[p.id] = p);
                    if (items.length === 0) {
                        $('#productResults').html('<div class="text-muted small">No products found.</div>');
                        return;
                    }

                    const cards = items.map(p => {
                        const isNew = p.product_type === 'new';
                        const stockText = isNew
                            ? `Stock: <span class="fw-semibold">${p.stock_quantity}</span>`
                            : `Devices: <span class="fw-semibold">${p.available_devices}</span>`;
                        const addBtn = isNew
                            ? `<button class="btn btn-md btn-primary w-100 add-qty-btn" data-product-id="${p.id}">Add</button>`
                            : `<span class="badge bg-warning w-100">IMEI Only</span>`;

                        const imgHtml = p.image
                            ? `<img src="${productImageBase}/${p.image}" class="img-fluid rounded mb-2" style="height:150px;object-fit:cover;width:100%;">`
                            : `<div class="bg-light rounded mb-2 d-flex align-items-center justify-content-center" style="height:90px;"><span class="text-muted small">No photo</span></div>`;

                        return `
                            <div class="col-12 col-md-6 col-xl-4 mb-2">
                                <div class="card h-100 shadow-sm pos-product-item" data-product-id="${p.id}">
                                    <div class="card-body py-3">
                                        ${imgHtml}
                                        <div class="fw-semibold mb-1">${p.brand} - ${p.label}</div>
                                        <div class="d-flex justify-content-between align-items-center mt-1">
                                            <div class="small text-muted mb-1">${p.brand}</div>
                                            <div class="small mb-1">${stockText}</div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-1">
                                            <div class="small">Price<br><span class="fw-semibold">${money(p.selling_price)} MMK</span></div>
                                            <div style="width:90px;">${addBtn}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('');

                    $('#productResults').html(`<div class="row g-2">${cards}</div>`);
                })
                .fail(function() {
                    $('#productResults').html('<div class="text-danger small">Failed to load products.</div>');
                });
        }

        $(document).on('click', '.add-qty-btn', function() {
            const id = Number($(this).data('product-id'));
            const product = productCache[id];
            if (!product) return;
            if (product.stock_quantity <= 0) {
                Swal.fire({ icon: 'warning', title: 'Out of stock', text: 'This product has no stock.' });
                return;
            }
            upsertQtyProduct(product, 1);
        });

        // IMEI add
        function loadDeviceByImei(imei) {
            if (!imei) return;
            $.get(urls.devices, { imei })
                .done(function(res) {
                    if (!res.data) {
                        Swal.fire({ icon: 'error', title: 'Not found', text: 'No available device with this IMEI.' });
                        return;
                    }
                    addImeiDevice(res.data);
                    $('#imeiInput').val('').focus();
                })
                .fail(function(xhr) {
                    const msg = xhr.responseJSON?.message || 'Failed to lookup device.';
                    Swal.fire({ icon: 'error', title: 'Error', text: msg });
                });
        }

        $('#imeiInput').on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                loadDeviceByImei($(this).val().trim());
            }
        });
        $('#imeiAddBtn').on('click', function() {
            loadDeviceByImei($('#imeiInput').val().trim());
        });

        // Cart edits
        $(document).on('click', '.remove-line-btn', function() {
            cart.splice(Number($(this).data('idx')), 1);
            renderCart();
        });
        $(document).on('input', '.qty-input', function() {
            const idx = Number($(this).data('idx'));
            const val = Math.max(1, Number($(this).val() || 1));
            cart[idx].quantity = val;
            renderCart();
        });
        $(document).on('input', '.price-input', function() {
            const idx = Number($(this).data('idx'));
            cart[idx].unit_price = Math.max(0, Number($(this).val() || 0));
            calcTotals();
            renderCart();
        });
        $(document).on('input', '.disc-input', function() {
            const idx = Number($(this).data('idx'));
            cart[idx].discount_price = Math.max(0, Number($(this).val() || 0));
            renderCart();
        });

        $('#clearCartBtn').on('click', function() {
            cart = [];
            renderCart();
        });

        // Installment UI
        $('input[name="paymentType"]').on('change', function() {
            const type = $('input[name="paymentType"]:checked').val();
            if (type === 'installment') {
                $('#installmentBox').show();
                $('#customerExtendedInfo').show();
            } else {
                $('#installmentBox').hide();
                $('#customerExtendedInfo').hide();
            }
            calcTotals();
        });
        $('#installmentRate, #downPayment').on('change input', function() {
            const totals = calcTotals();
            updateInstallmentPreview(totals.grand);
        });

        function updateInstallmentPreview(grandTotal) {
            const $opt = $('#installmentRate option:selected');
            const months = Number($opt.data('months') || 0);
            const rate = Number($opt.data('rate') || 0);
            const down = Math.max(0, Number($('#downPayment').val() || 0));
            const totalWithInterest = Number(grandTotal) * (1 + (rate / 100));
            const remaining = Math.max(0, totalWithInterest - down);
            const monthly = months > 0 ? (remaining / months) : remaining;
            $('#totalWithInterest').text(money(totalWithInterest));
            $('#monthlyAmount').text(money(monthly));
        }

        // Checkout
        $('#checkoutBtn').on('click', function() {
            if (cart.length === 0) {
                Swal.fire({ icon: 'warning', title: 'Empty cart', text: 'Please add items to cart.' });
                return;
            }

            const payment_type = $('input[name="paymentType"]:checked').val();
            const customer_name = $('#customerName').val().trim();
            if (!customer_name) {
                Swal.fire({ icon: 'warning', title: 'Name required', text: 'Customer name is required for the receipt.' });
                return;
            }

            const formData = new FormData();
            
            // Items
            cart.forEach((l, i) => {
                formData.append(`items[${i}][product_id]`, l.product_id);
                formData.append(`items[${i}][quantity]`, l.device_id ? 1 : l.quantity);
                if (l.device_id) formData.append(`items[${i}][device_id]`, l.device_id);
                if (l.imei) formData.append(`items[${i}][imei]`, l.imei);
                formData.append(`items[${i}][unit_price]`, Number(l.unit_price));
                formData.append(`items[${i}][discount_price]`, Number(l.discount_price || 0));
            });

            formData.append('customer_name', customer_name);
            formData.append('customer_phone', $('#customerPhone').val());
            formData.append('customer_nrc', $('#customerNRC').val());
            formData.append('customer_address', $('#customerAddress').val());
            formData.append('payment_type', payment_type);

            if (payment_type === 'installment' && !$('#customerNRC').val().trim()) {
                Swal.fire({ icon: 'warning', title: 'NRC required', text: 'NRC is required for installment sales.' });
                return;
            }

            if (payment_type === 'installment') {
                formData.append('installment_rate_id', $('#installmentRate').val() || '');
                formData.append('down_payment', Number($('#downPayment').val() || 0));
            }

            // Attachments
            const files = $('#customerAttachments')[0].files;
            for (let i = 0; i < files.length; i++) {
                formData.append('attachments[]', files[i]);
            }

            if (payment_type === 'installment' && !$('#installmentRate').val()) {
                Swal.fire({ icon: 'warning', title: 'Missing plan', text: 'Please select an installment plan.' });
                return;
            }

            $('#checkoutBtn').prop('disabled', true).text('Processing…');
            $.ajax({
                url: urls.checkout,
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf },
                processData: false,
                contentType: false,
                data: formData,
                success: function(res) {
                    const receiptUrl = "{{ route('admin.order.receipt', ':id') }}?print=1".replace(':id', res.data.order_id);
                    window.location.href = receiptUrl;
                },
                error: function(xhr) {
                    let msg = xhr.responseJSON?.message || 'Checkout failed.';
                    if (xhr.responseJSON?.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('\\n');
                    }
                    Swal.fire({ icon: 'error', title: 'Error', text: msg });
                },
                complete: function() {
                    $('#checkoutBtn').prop('disabled', false).text('Checkout');
                }
            });
        });

        // Initial load
        loadProducts('');
        renderCart();
        $('#imeiInput').focus();
    });
</script>
@endsection

