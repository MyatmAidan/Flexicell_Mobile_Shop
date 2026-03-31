@extends('layouts.app')

@section('meta')
    <meta name="description" content="Trade-In Sale">
@endsection

@section('title', 'Trade-In Sale')

@section('style')
<style>
    /* ── Product card ── */
    .pos-panel { min-height: 70vh; }
    .pos-scroll { max-height: 55vh; overflow: auto; }
    .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Courier New", monospace; }

    .pos-product-item { cursor: pointer; transition: transform 0.15s, box-shadow 0.15s; border: 1px solid #e8eaed; border-radius: 8px; overflow: hidden; }
    .pos-product-item:hover { transform: translateY(-2px); box-shadow: 0 4px 14px rgba(0,0,0,0.1); }
    .pos-product-item .card-body { padding: 0 !important; }
    .pos-product-item .product-thumb { width: 100%; height: 90px; background: linear-gradient(135deg,#f0f4f8,#e2e8f0); display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .pos-product-item .product-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .pos-product-item .product-thumb .thumb-icon { font-size: 2rem; color: #b0bec5; }
    .pos-product-item .product-meta { padding: 8px 10px; }
    .pos-product-item .product-meta .product-name { font-size: 0.8rem; font-weight: 600; line-height: 1.3; margin-bottom: 5px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .pos-product-item .product-meta .product-footer { display: flex; justify-content: space-between; align-items: center; }
    .pos-product-item .product-meta .stock-badge { font-size: 0.7rem; color: #64748b; }

    /* ── Variant tile ── */
    .variant-group-label { font-size: 0.85rem; font-weight: 600; color: #666; margin-bottom: 8px; display: block; }
    .variant-tiles { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
    .variant-tile-input { display: none; }
    .variant-tile-label { padding: 6px 14px; border: 2px solid #edeff2; border-radius: 8px; cursor: pointer; font-size: 0.88rem; transition: all 0.2s; background: #fff; user-select: none; min-width: 56px; text-align: center; }
    .variant-tile-input:checked + .variant-tile-label { border-color: #0d6efd; background-color: #f0f7ff; color: #0d6efd; font-weight: 600; }

    /* ── Color swatches ── */
    .color-swatches { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 14px; }
    .color-swatch-input { display: none; }
    .color-swatch-label { width: 32px; height: 32px; border-radius: 50%; cursor: pointer; border: 2px solid #edeff2; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
    .color-swatch-input:checked + .color-swatch-label { border-color: #0d6efd; transform: scale(1.15); box-shadow: 0 0 0 2px #fff, 0 0 0 4px #0d6efd; }
    .color-swatch-label .check-mark { color: #fff; font-size: 12px; display: none; text-shadow: 0 1px 2px rgba(0,0,0,.5); }
    .color-swatch-input:checked + .color-swatch-label .check-mark { display: block; }

    .variant-combo-input { position: absolute; opacity: 0; width: 0; height: 0; }
    .variant-combo-wrap { position: relative; }
    .variant-combo-card {
        border: 2px solid #edeff2;
        border-radius: 10px;
        padding: 10px 12px;
        cursor: pointer;
        transition: all 0.2s;
        background: #fff;
    }
    .variant-combo-input:focus-visible + .variant-combo-card { outline: 2px solid #0d6efd; }
    .variant-combo-input:checked + .variant-combo-card {
        border-color: #0d6efd;
        background: #f0f7ff;
        box-shadow: 0 2px 6px rgba(13, 110, 253, 0.12);
    }
    .variant-combo-meta { font-size: 0.72rem; color: #64748b; }

    /* ── Trade-in credit badge ── */
    .tradein-credit-badge { font-size: 0.78rem; color: #198754; font-weight: 600; background: #d1f0e0; border-radius: 6px; padding: 2px 8px; }

    /* ── Drag & drop ── */
    .pos-product-item.dragging { opacity: 0.45; transform: scale(0.95); box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
    #cartDropZone.drag-over { border-color: #0d6efd !important; background: #e8f0fe; color: #0d6efd; }
    #cartTable tbody.drag-over-body tr:first-child td { background: #e8f0fe; }
</style>
@endsection

@section('content')
<div class="d-flex align-items-center mb-3 gap-2">
    <h4 class="mb-0"><i class="fas fa-exchange-alt me-2 text-primary"></i>Trade-In Sale</h4>
    <span class="badge bg-primary ms-2">Customer brings old phone → buys new phone</span>
</div>

{{-- ══════════════════════════ TOP ROW ══════════════════════════ --}}
<div class="row g-3 mb-3">

    {{-- LEFT: Trade-in phone form ──────────────────────────────── --}}
    <div class="col-12 col-lg-5">
        <div class="card h-100">
            <div class="card-header bg-warning-subtle d-flex align-items-center gap-2">
                <i class="fas fa-mobile-alt text-warning"></i>
                <strong>Trade-In Phone Details</strong>
                <small class="text-muted ms-auto">(customer's old phone)</small>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-12">
                        <label class="form-label mb-1">Phone Model <span class="text-danger">*</span></label>
                        <select id="ti_phone_model" class="form-control form-select" required>
                            <option value="">Select model…</option>
                            @foreach($phoneModels as $m)
                                <option value="{{ $m->id }}">{{ $m->brand->brand_name ?? '' }} {{ $m->model_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1">IMEI <span class="text-danger">*</span></label>
                        <input type="text" id="ti_imei" class="form-control mono" placeholder="Enter IMEI" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1">Condition Grade <span class="text-danger">*</span></label>
                        <select id="ti_condition" class="form-control form-select" required>
                            <option value="A">A – Like New</option>
                            <option value="B" selected>B – Good</option>
                            <option value="C">C – Used</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1">RAM</label>
                        <select id="ti_ram" class="form-control form-select">
                            <option value="">Select / leave blank</option>
                            @foreach($ramOptions as $opt)
                                <option value="{{ $opt->value }}">{{ $opt->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1">Storage</label>
                        <select id="ti_storage" class="form-control form-select">
                            <option value="">Select / leave blank</option>
                            @foreach($storageOptions as $opt)
                                <option value="{{ $opt->value }}">{{ $opt->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1">Color</label>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="color" id="ti_color_picker" value="#808080" class="form-control form-control-color" style="width:42px;height:38px;">
                            <select id="ti_color_select" class="form-control form-select">
                                <option value="">Select / leave blank</option>
                                @foreach($colorOptions as $opt)
                                    <option value="{{ $opt->value }}">{{ $opt->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" id="ti_color_hidden" value="">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1">Battery % <span class="text-danger">*</span></label>
                        <input type="number" id="ti_battery" class="form-control" min="0" max="100" value="80" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1">Trade-In Value (Buy Price) <span class="text-danger">*</span></label>
                        <input type="number" id="ti_buy_price" class="form-control" min="0" step="0.01" value="0" required>
                        <small class="text-success">This amount will be deducted from the sale.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1">Purchase Date</label>
                        <input type="datetime-local" id="ti_purchase_at" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label mb-1">Phone Images (optional)</label>
                        <input type="file" id="ti_image_input" class="form-control" multiple accept="image/*">
                        <div id="ti_image_preview" class="mt-2 d-flex flex-wrap gap-2"></div>
                        <div id="ti_images_hidden"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: New product selection ───────────────────────────── --}}
    <div class="col-12 col-lg-7">
        <div class="card pos-panel h-100">
            <div class="card-header bg-primary-subtle d-flex align-items-center gap-2">
                <i class="fas fa-shopping-cart text-primary"></i>
                <strong>Select New Product</strong>
                <small class="text-muted ms-auto">(product customer wants to buy)</small>
            </div>
            <div class="card-body">
                <div class="row mb-2 align-items-end g-2">
                    <div class="col-md-7">
                        <label class="form-label mb-1">Search product</label>
                        <input type="text" id="productSearch" class="form-control" placeholder="Search by model name…">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label mb-1">Quick IMEI</label>
                        <div class="input-group">
                            <input type="text" id="imeiInput" class="form-control mono" placeholder="Scan / type IMEI">
                            <button class="btn btn-primary" id="imeiAddBtn" type="button">Add</button>
                        </div>
                    </div>
                </div>
                <div class="pos-scroll border rounded p-2" id="productResults">
                    <div class="text-muted small">Type to search products…</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════ BOTTOM ROW ══════════════════════════ --}}
<div class="row g-3">

    {{-- CUSTOMER + PAYMENT ─────────────────────────────────────────── --}}
    <div class="col-12 col-lg-5">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">Customer &amp; Payment</h5>

                {{-- Customer search --}}
                <div class="mb-2 position-relative">
                    <label class="form-label">Search Customer</label>
                    <input type="text" id="customerSearchInput" class="form-control" placeholder="Name / phone / email…" autocomplete="off">
                    <input type="hidden" id="customerId" value="">
                    <div id="customerDropdown" class="border rounded bg-white shadow-sm position-absolute w-100" style="display:none; z-index:1050; max-height:200px; overflow-y:auto; top:100%;"></div>
                    <div id="customerSelected" class="small mt-1 text-success fw-semibold" style="display:none;"></div>
                </div>

                {{-- Toggle: create new customer --}}
                <div class="mb-3">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="toggleNewCustomer">
                        <i class="fas fa-user-plus me-1"></i> New customer
                    </button>
                </div>
                <div id="newCustomerForm" style="display:none;">
                    <div class="mb-2">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" id="customerName" class="form-control" placeholder="Full name">
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label">Phone</label>
                            <input type="text" id="customerPhone" class="form-control" placeholder="Optional">
                        </div>
                        <div class="col-6">
                            <label class="form-label">NRC</label>
                            <input type="text" id="customerNRC" class="form-control" placeholder="Optional">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea id="customerAddress" class="form-control" rows="2" placeholder="Optional"></textarea>
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

                <div id="installmentBox" class="border rounded p-2 mb-3" style="display:none;">
                    <div class="mb-2">
                        <label class="form-label">Plan</label>
                        <select class="form-control" id="installmentRate">
                            <option value="">Select plan</option>
                            @foreach($installmentRates as $r)
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
                    <div class="d-flex justify-content-between"><span class="text-muted">Subtotal</span><span id="subTotal">0.00</span></div>
                    <div class="d-flex justify-content-between"><span class="text-muted">Discount</span><span id="discTotal">0.00</span></div>
                    <div class="d-flex justify-content-between text-success"><span class="text-muted">Trade-In Credit</span><span id="creditTotal">− 0.00</span></div>
                    <hr class="my-1">
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Grand Total</span>
                        <span id="grandTotal">0.00</span>
                    </div>
                </div>

                <button class="btn btn-success w-100" id="checkoutBtn"><i class="fas fa-check me-1"></i> Checkout Trade-In Sale</button>
            </div>
        </div>
    </div>

    {{-- CART ──────────────────────────────────────────────────────── --}}
    <div class="col-12 col-lg-7">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Shopping Cart</h5>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="clearCartBtn">Clear</button>
                </div>

                {{-- Drop zone hint (shows while dragging) --}}
                <div id="cartDropZone" class="rounded mb-2 p-2 text-center text-muted small" style="border:2px dashed #ced4da; display:none;">
                    <i class="fas fa-cart-plus me-1"></i> Drop product here to add to cart
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle" id="cartTable">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Type</th>
                                <th style="width:80px">Qty</th>
                                <th style="width:100px">Price</th>
                                <th style="width:100px">Disc</th>
                                <th style="width:90px">Total</th>
                                <th style="width:1%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="7" class="text-muted small">Cart is empty.</td></tr>
                        </tbody>
                    </table>
                </div>

                {{-- Trade-in credit summary ── --}}
                <div class="mt-2 p-2 rounded bg-success-subtle" id="tradeInCreditRow" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-exchange-alt me-1 text-success"></i><strong>Trade-In Credit</strong></span>
                        <span class="tradein-credit-badge" id="tradeInCreditDisplay">− 0.00</span>
                    </div>
                    <small class="text-muted" id="tradeInModelDisplay"></small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Variant Modal (same as Direct Sale) ── --}}
<div class="modal fade" id="variantModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Variant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="variantProductId">
                <div class="text-center mb-3">
                    <img id="modalVariantImg" src="" alt="" onerror="this.src='https://via.placeholder.com/300?text=No+Photo'" style="width:100%;max-height:140px;object-fit:contain;background:#f8f9fa;border-radius:10px;">
                    <h6 class="fw-bold mb-0 mt-2" id="variantProductName"></h6>
                </div>
                <div id="variantCombinationSection" class="mb-3" style="display:none;">
                    <label class="variant-group-label">In-stock SKU <span class="text-muted fw-normal" style="font-size:0.78rem;">(product_variants + devices)</span></label>
                    <div id="variantCombinationList" class="d-flex flex-column gap-2"></div>
                    <p class="small text-muted mt-2 mb-0">Counts include placeholder units pending IMEI.</p>
                </div>
                <div id="variantLegacySection">
                    <div class="mb-3">
                        <label class="variant-group-label">RAM</label>
                        <div class="variant-tiles" id="variantRamList"></div>
                    </div>
                    <div class="mb-3">
                        <label class="variant-group-label">Storage</label>
                        <div class="variant-tiles" id="variantStorageList"></div>
                    </div>
                    <div class="mb-3">
                        <label class="variant-group-label d-flex justify-content-between">
                            <span>Color</span>
                            <span id="selectedColorName" class="fw-bold px-2 py-1 rounded" style="font-size:.78rem;background:#eee;">Any</span>
                        </label>
                        <div class="color-swatches" id="variantColorList"></div>
                    </div>
                </div>
                <div class="row g-2 align-items-center">
                    <div class="col-6">
                        <label class="variant-group-label mb-0">Quantity</label>
                        <input type="number" class="form-control" id="variantQty" min="1" value="1">
                    </div>
                    <div class="col-6">
                        <div class="p-2 border rounded bg-light">
                            <span class="d-block text-muted" style="font-size:.7rem;">AVAILABILITY</span>
                            <span id="variantStockDisplay" class="fw-bold">-</span>
                        </div>
                    </div>
                </div>
                <div class="mt-2 p-2 border rounded bg-light" id="variantPriceBox" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-semibold" style="font-size:.82rem;">SELLING PRICE</span>
                        <span id="variantPriceDisplay" class="fw-bold fs-5 text-primary">-</span>
                    </div>
                    <input type="hidden" id="variantResolvedPrice" value="0">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="variantAddBtn">Add to cart</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(function () {
    const csrf = $('meta[name="csrf-token"]').attr('content');
    const urls = {
        products:       "{{ route('admin.direct_sale.products') }}",
        devices:        "{{ route('admin.direct_sale.devices') }}",
        variants:       "{{ route('admin.direct_sale.variants', ['product' => '__id__']) }}",
        variants_stock: "{{ route('admin.direct_sale.variants_stock') }}",
        customers:      "{{ route('admin.trade_in.customers') }}",
        checkout:       "{{ route('admin.trade_in.checkout') }}"
    };
    const productImageBase = "{{ asset('storage/products') }}";

    let cart = [];
    let productCache = {};
    const variantModal = new bootstrap.Modal(document.getElementById('variantModal'));
    let variantUiMode = 'legacy';
    let variantCombinations = [];

    // ── Helpers ──────────────────────────────────────────────────────
    function money(n) { return (Number(n || 0)).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }

    function escapeHtml(str) {
        if (str == null || str === '') return '';
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    function getTradeInCredit() {
        return Math.max(0, parseFloat($('#ti_buy_price').val()) || 0);
    }

    function calcTotals() {
        let sub = 0, disc = 0;
        cart.forEach(l => {
            sub  += Number(l.unit_price) * Number(l.quantity);
            disc += Number(l.discount_price) * Number(l.quantity);
        });
        const credit = getTradeInCredit();
        const grand  = Math.max(0, sub - disc - credit);
        $('#subTotal').text(money(sub));
        $('#discTotal').text(money(disc));
        $('#creditTotal').text('− ' + money(credit));
        $('#grandTotal').text(money(grand));
        $('#tradeInCreditDisplay').text('− ' + money(credit));
        $('#tradeInCreditRow').toggle(credit > 0);
        const model = $('#ti_phone_model option:selected').text();
        $('#tradeInModelDisplay').text(model ? 'Model: ' + model : '');
        updateInstallmentPreview(grand);
        return { sub, disc, credit, grand };
    }

    // ── Cart render ───────────────────────────────────────────────────
    function renderCart() {
        const $tbody = $('#cartTable tbody');
        $tbody.empty();
        if (!cart.length) {
            $tbody.append('<tr><td colspan="7" class="text-muted small">Cart is empty.</td></tr>');
            calcTotals();
            return;
        }
        cart.forEach((l, idx) => {
            const lineTotal = (Number(l.unit_price) - Number(l.discount_price)) * Number(l.quantity);
            const qtyInput = l.device_id
                ? `<span class="text-muted">1</span>`
                : `<input type="number" class="form-control form-control-sm qty-input" data-idx="${idx}" min="1" value="${l.quantity}" style="width:60px;">`;
            $tbody.append(`
                <tr>
                    <td>
                        <div class="fw-semibold small">${escapeHtml(l.product_label)}</div>
                        ${l.variant_label ? `<div class="small text-muted">${escapeHtml(l.variant_label)}</div>` : ''}
                        ${l.device_id ? `<div class="small text-muted mono">IMEI: ${escapeHtml(l.imei || '-')}</div>` : ''}
                    </td>
                    <td><span class="badge bg-${l.device_id ? 'info' : 'secondary'}">${l.device_id ? 'IMEI' : 'QTY'}</span></td>
                    <td>${qtyInput}</td>
                    <td><input type="number" class="form-control form-control-sm price-input" data-idx="${idx}" min="0" step="0.01" value="${l.unit_price}" style="width:80px;"></td>
                    <td><input type="number" class="form-control form-control-sm disc-input" data-idx="${idx}" min="0" step="0.01" value="${l.discount_price}" style="width:80px;"></td>
                    <td class="fw-semibold small">${money(lineTotal)}</td>
                    <td><button class="btn btn-sm btn-outline-danger remove-line-btn" data-idx="${idx}"><i class="fa fa-trash"></i></button></td>
                </tr>
            `);
        });
        calcTotals();
    }

    // Cart events
    $(document).on('input', '.qty-input',   function() { cart[$(this).data('idx')].quantity       = Math.max(1, parseInt($(this).val()) || 1); calcTotals(); });
    $(document).on('input', '.price-input', function() { cart[$(this).data('idx')].unit_price      = parseFloat($(this).val()) || 0;            calcTotals(); });
    $(document).on('input', '.disc-input',  function() { cart[$(this).data('idx')].discount_price  = parseFloat($(this).val()) || 0;            calcTotals(); });
    $(document).on('click', '.remove-line-btn', function() { cart.splice($(this).data('idx'), 1); renderCart(); });
    $('#clearCartBtn').on('click', function() { cart = []; renderCart(); });
    $('#ti_buy_price').on('input', calcTotals);

    // ── Installment preview ───────────────────────────────────────────
    function updateInstallmentPreview(grand) {
        const $sel = $('#installmentRate option:selected');
        const months = parseInt($sel.data('months')) || 0;
        const rate   = parseFloat($sel.data('rate')) || 0;
        const down   = parseFloat($('#downPayment').val()) || 0;
        if (!months) { $('#totalWithInterest,#monthlyAmount').text('0.00'); return; }
        const totalInt = grand * (1 + rate / 100);
        const remaining = Math.max(0, totalInt - down);
        const monthly = months > 0 ? remaining / months : 0;
        $('#totalWithInterest').text(money(totalInt));
        $('#monthlyAmount').text(money(monthly));
    }
    $('input[name="paymentType"]').on('change', function() { $('#installmentBox').toggle($(this).val() === 'installment'); });
    $('#installmentRate, #downPayment').on('change input', function() { const { grand } = calcTotals(); updateInstallmentPreview(grand); });

    // ── Customer search ───────────────────────────────────────────────
    let customerTimer;
    $('#customerSearchInput').on('input', function() {
        clearTimeout(customerTimer);
        const q = $(this).val().trim();
        if (!q) { $('#customerDropdown').hide(); return; }
        customerTimer = setTimeout(() => {
            $.get(urls.customers, { q }).done(function(res) {
                const items = res.data || [];
                const $dd = $('#customerDropdown').empty();
                if (!items.length) {
                    $dd.html('<div class="p-2 text-muted small">No customers found.</div>').show();
                    return;
                }
                items.forEach(c => {
                    $dd.append(`<div class="p-2 border-bottom customer-option" style="cursor:pointer;"
                        data-id="${c.id}" data-name="${c.name}" data-phone="${c.phone||''}" data-address="${c.address||''}">
                        <div class="fw-semibold small">${c.name}</div>
                        <div class="text-muted" style="font-size:.75rem;">${c.phone||''} ${c.email||''}</div>
                    </div>`);
                });
                $dd.show();
            });
        }, 250);
    });

    $(document).on('click', '.customer-option', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const phone = $(this).data('phone');
        $('#customerId').val(id);
        $('#customerSearchInput').val(name);
        $('#customerDropdown').hide();
        $('#customerSelected').text('Selected: ' + name + (phone ? ' (' + phone + ')' : '')).show();
        $('#newCustomerForm').hide();
        $('#toggleNewCustomer').show().html('<i class="fas fa-user-plus me-1"></i> New customer instead');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#customerSearchInput,#customerDropdown').length) {
            $('#customerDropdown').hide();
        }
    });

    $('#toggleNewCustomer').on('click', function() {
        const showing = $('#newCustomerForm').is(':visible');
        if (showing) {
            $('#newCustomerForm').hide();
            $(this).html('<i class="fas fa-user-plus me-1"></i> New customer');
        } else {
            $('#newCustomerForm').show();
            $(this).html('<i class="fas fa-times me-1"></i> Cancel');
            $('#customerId').val('');
            $('#customerSearchInput').val('');
            $('#customerSelected').hide();
        }
    });

    // ── Color picker sync (trade-in phone) ───────────────────────────
    $('#ti_color_picker').on('input', function() { $('#ti_color_hidden').val($(this).val()); $('#ti_color_select').val(''); });
    $('#ti_color_select').on('change', function() {
        const v = $(this).val();
        if (v) { $('#ti_color_picker').val(v.startsWith('#') ? v : '#808080'); $('#ti_color_hidden').val(v); }
    });

    // ── Trade-in image upload ─────────────────────────────────────────
    $('#ti_image_input').on('change', function() {
        Array.from(this.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const b64 = e.target.result;
                const id  = 'ti_img_' + Date.now();
                $('#ti_image_preview').append(`
                    <div class="position-relative" id="${id}_wrap">
                        <img src="${b64}" style="width:70px;height:70px;object-fit:cover;border-radius:6px;border:1px solid #ddd;">
                        <button type="button" class="btn btn-danger btn-sm position-absolute" style="top:-4px;right:-4px;padding:1px 5px;font-size:0.7rem;" onclick="$('#${id}_wrap').remove()"><i class="fa fa-times"></i></button>
                        <input type="hidden" class="ti-img-hidden" value="${b64}">
                    </div>
                `);
            };
            reader.readAsDataURL(file);
        });
        $(this).val('');
    });

    // ── Product search ────────────────────────────────────────────────
    let searchTimer;
    $('#productSearch').on('input', function() {
        clearTimeout(searchTimer);
        const q = $(this).val();
        searchTimer = setTimeout(() => loadProducts(q), 250);
    });

    function loadProducts(q) {
        $('#productResults').html('<div class="text-muted small p-2">Loading…</div>');
        $.get(urls.products, { q }).done(function(res) {
            const items = res.data || [];
            productCache = {};
            items.forEach(p => productCache[p.id] = p);
            if (!items.length) { $('#productResults').html('<div class="text-muted small p-2">No products found.</div>'); return; }
            renderProducts(items);
        }).fail(() => $('#productResults').html('<div class="text-danger small p-2">Failed to load products.</div>'));
    }

    function renderProducts(items) {
        const html = items.map(p => {
            const isNew = p.product_type === 'new';
            const stockText = isNew ? `Stock: <span class="fw-bold">${p.stock_quantity}</span>` : `Devices: <span class="fw-bold">${p.available_devices}</span>`;
            const addBtn = isNew
                ? `<button class="btn btn-xs btn-primary add-qty-btn" data-product-id="${p.id}" style="padding:2px 8px;font-size:0.72rem;"><i class="fas fa-plus"></i></button>`
                : `<span class="badge bg-warning text-dark" style="font-size:.65rem;">IMEI</span>`;
            const thumb = p.image
                ? `<img src="${productImageBase}/${p.image}" alt="">`
                : `<i class="fas fa-mobile-alt thumb-icon"></i>`;
            return `
                <div class="col-4 col-md-3 col-lg-4 mb-2">
                    <div class="card shadow-sm pos-product-item"
                         draggable="true"
                         data-product-id="${p.id}"
                         data-product-type="${p.product_type}"
                         title="Drag to cart or click Add">
                        <div class="card-body">
                            <div class="product-thumb">${thumb}</div>
                            <div class="product-meta">
                                <div class="product-name">${p.brand} – ${p.label}</div>
                                <div class="product-footer">
                                    <div class="stock-badge">${stockText}</div>
                                    <div class="text-primary fw-bold" style="font-size:.72rem;">${money(p.selling_price)}</div>
                                </div>
                                <div class="mt-1 text-end">${addBtn}</div>
                            </div>
                        </div>
                    </div>
                </div>`;
        }).join('');
        $('#productResults').html(`<div class="row g-1">${html}</div>`);
        bindDragEvents();
    }

    // ── Drag & drop ───────────────────────────────────────────────────
    let dragProductId = null;

    function bindDragEvents() {
        document.querySelectorAll('.pos-product-item[draggable="true"]').forEach(el => {
            el.addEventListener('dragstart', function(e) {
                dragProductId = this.dataset.productId;
                this.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'copy';
                // Show drop zone
                document.getElementById('cartDropZone').style.display = 'block';
            });
            el.addEventListener('dragend', function() {
                this.classList.remove('dragging');
                dragProductId = null;
                document.getElementById('cartDropZone').style.display = 'none';
                document.getElementById('cartDropZone').classList.remove('drag-over');
                document.querySelector('#cartTable tbody').classList.remove('drag-over-body');
            });
        });
    }

    // Drop zone events
    const $dropZone  = document.getElementById('cartDropZone');
    const $cartTbody = document.querySelector('#cartTable tbody');

    [$dropZone, $cartTbody].forEach(el => {
        el.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'copy';
            $dropZone.classList.add('drag-over');
            $cartTbody.classList.add('drag-over-body');
        });
        el.addEventListener('dragleave', function() {
            $dropZone.classList.remove('drag-over');
            $cartTbody.classList.remove('drag-over-body');
        });
        el.addEventListener('drop', function(e) {
            e.preventDefault();
            $dropZone.classList.remove('drag-over');
            $cartTbody.classList.remove('drag-over-body');
            if (!dragProductId) return;
            const pid = parseInt(dragProductId);
            const p   = productCache[pid];
            if (!p) return;
            if (p.product_type === 'new') {
                if (p.stock_quantity <= 0) {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'warning', title: 'Out of stock', timer: 1500, showConfirmButton: false });
                    return;
                }
                openVariantModalForProduct(p);
            } else {
                Swal.fire({ icon: 'info', title: 'Scan IMEI', text: 'Second-hand devices must be added via IMEI scan.' });
            }
        });
    });

    // Add by quantity (variant modal)
    $(document).on('click', '.add-qty-btn', function(e) {
        e.stopPropagation();
        const pid = $(this).data('product-id');
        const p = productCache[pid];
        if (!p) return;
        if (p.stock_quantity <= 0) {
            Swal.fire({ icon: 'warning', title: 'Out of stock', text: 'This product has no stock.' });
            return;
        }
        openVariantModalForProduct(p);
    });
    $(document).on('click', '.pos-product-item', function() {
        const pid = $(this).data('product-id');
        const p   = productCache[pid];
        if (!p) return;
        if (p.product_type === 'new') {
            if (p.stock_quantity <= 0) {
                Swal.fire({ toast: true, position: 'top-end', icon: 'warning', title: 'Out of stock', timer: 1500, showConfirmButton: false });
                return;
            }
            openVariantModalForProduct(p);
        }
    });

    // IMEI direct add
    $('#imeiAddBtn').on('click', lookupImei);
    $('#imeiInput').on('keydown', function(e) { if (e.key === 'Enter') lookupImei(); });

    function lookupImei() {
        const imei = $('#imeiInput').val().trim();
        if (!imei) return;
        $.get(urls.devices, { imei }).done(function(res) {
            if (!res.data) { Swal.fire({ icon: 'warning', title: 'Not found', text: res.message || 'Device not found.' }); return; }
            addImeiDevice(res.data);
            $('#imeiInput').val('');
        }).fail(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to look up device.' }));
    }

    function addImeiDevice(d) {
        const key = `d-${d.device_id}`;
        if (cart.some(x => x.key === key)) { Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: 'Already in cart', timer: 1500, showConfirmButton: false }); return; }
        cart.push({ key, product_id: d.product_id, product_label: `${d.product_label} - ${d.brand}`, product_type: 'second hand', quantity: 1, unit_price: Number(d.selling_price || 0), discount_price: 0, device_id: d.device_id, imei: d.imei, ram_option_id: null, storage_option_id: null, color_option_id: null });
        renderCart();
    }

    // ── Variant Modal (combo + legacy, aligned with Direct Sale API) ──
    function renderLegacyVariantPickers(v) {
        const renderTiles = (container, items, name) => {
            const $c = $(container).empty();
            $c.append(`<div class="variant-tile shadow-sm"><input type="radio" name="${name}" id="${name}_any" value="" class="variant-tile-input variant-option" checked><label for="${name}_any" class="variant-tile-label">Any</label></div>`);
            (items || []).forEach(x => {
                $c.append(`<div class="variant-tile shadow-sm"><input type="radio" name="${name}" id="${name}_${x.id}" value="${x.id}" data-label="${escapeHtml(x.name)}" data-value="${escapeHtml(x.value)}" class="variant-tile-input variant-option"><label for="${name}_${x.id}" class="variant-tile-label">${escapeHtml(x.name)}</label></div>`);
            });
        };
        const renderSwatchesLocal = (container, items) => {
            const $c = $(container).empty();
            $c.append(`<div class="color-swatch"><input type="radio" name="variant_color" id="color_any" value="" class="color-swatch-input variant-option" checked><label for="color_any" class="color-swatch-label" style="background:#eee;color:#666;" title="Any"><i class="fas fa-ban fa-xs"></i></label></div>`);
            (items || []).forEach(x => {
                $c.append(`<div class="color-swatch"><input type="radio" name="variant_color" id="color_${x.id}" value="${x.id}" data-label="${escapeHtml(x.name)}" data-value="${escapeHtml(x.value)}" class="color-swatch-input variant-option"><label for="color_${x.id}" class="color-swatch-label" style="background-color:${escapeHtml(x.value)};" title="${escapeHtml(x.name)}"><i class="fas fa-check check-mark"></i></label></div>`);
            });
        };
        renderTiles('#variantRamList', v.ram, 'variant_ram');
        renderTiles('#variantStorageList', v.storage, 'variant_storage');
        renderSwatchesLocal('#variantColorList', v.color);
    }

    function renderCombinationPickers(combos) {
        const $list = $('#variantCombinationList').empty();
        combos.forEach((c, idx) => {
            const id = 'vc_' + c.product_variant_id;
            const minP = c.min_price;
            const maxP = c.max_price;
            let priceHint = '';
            if (minP != null) {
                priceHint = (minP === maxP) ? money(minP) : (money(minP) + ' – ' + money(maxP));
            }
            $list.append(
                '<div class="variant-combo-wrap">' +
                '<input type="radio" name="variant_combo_pick" id="' + id + '" class="variant-combo-input" value="' + c.product_variant_id + '"' + (idx === 0 ? ' checked' : '') + ' data-stock="' + c.stock + '">' +
                '<label for="' + id + '" class="variant-combo-card d-block mb-0">' +
                '<div class="d-flex justify-content-between align-items-start gap-2">' +
                '<span class="fw-semibold small">' + escapeHtml(c.label) + '</span>' +
                '<span class="badge bg-success flex-shrink-0">' + c.stock + ' avail.</span>' +
                '</div>' +
                (priceHint ? '<div class="variant-combo-meta mt-1">' + escapeHtml(priceHint) + '</div>' : '') +
                '</label></div>'
            );
        });
    }

    function wireVariantModalAfterLoad() {
        $('input[name="variant_combo_pick"]').off('change.vsku').on('change.vsku', function() { checkVariantStock(); });
        $('.variant-option').off('change.vstk').on('change.vstk', function() { checkVariantStock(); });
    }

    function checkVariantStock() {
        const productId = $('#variantProductId').val();
        if (!productId) return;

        $('#variantStockDisplay').html('<span class="spinner-border spinner-border-sm text-secondary"></span>');
        $('#variantAddBtn').prop('disabled', true);

        let stockParams = { product_id: productId };
        if (variantUiMode === 'combo') {
            const $pick = $('input[name="variant_combo_pick"]:checked');
            if (!$pick.length) {
                $('#variantStockDisplay').html('<span class="text-muted">—</span>');
                return;
            }
            stockParams.product_variant_id = $pick.val();
        } else {
            stockParams.ram = $('input[name="variant_ram"]:checked').val();
            stockParams.storage = $('input[name="variant_storage"]:checked').val();
            stockParams.color = $('input[name="variant_color"]:checked').val();
            const colorName = $('input[name="variant_color"]:checked').data('label') || 'Any';
            $('#selectedColorName').text(colorName);
        }

        $.get(urls.variants_stock, stockParams)
            .done(function(res) {
                const count = res.count || 0;
                if (count > 0) {
                    $('#variantStockDisplay').html('<span class="text-success">' + count + ' Available</span>');
                    $('#variantAddBtn').prop('disabled', false);
                    if (variantUiMode === 'combo') {
                        $('#variantQty').attr('max', count);
                    }
                } else {
                    $('#variantStockDisplay').html('<span class="text-danger">Out of Stock</span>');
                    $('#variantAddBtn').prop('disabled', true);
                    if (variantUiMode === 'combo') {
                        $('#variantQty').removeAttr('max');
                    }
                }

                if (res.min_price !== null) {
                    $('#variantPriceDisplay').text(money(res.min_price) + (res.min_price !== res.max_price ? ' – ' + money(res.max_price) : ''));
                    $('#variantResolvedPrice').val(res.min_price);
                    $('#variantPriceBox').show();
                } else {
                    const product = productCache[Number(productId)];
                    const fallback = product ? product.selling_price : 0;
                    $('#variantPriceDisplay').text(money(fallback));
                    $('#variantResolvedPrice').val(fallback);
                    $('#variantPriceBox').show();
                }
            })
            .fail(function() {
                $('#variantStockDisplay').html('<span class="text-muted">Error</span>');
                $('#variantPriceBox').hide();
                $('#variantAddBtn').prop('disabled', true);
            });
    }

    function openVariantModalForProduct(product) {
        if (!product) return;
        $('#modalVariantImg').attr('src', product.image ? `${productImageBase}/${product.image}` : '');
        $('#variantProductId').val(product.id);
        $('#variantProductName').text(`${product.brand} - ${product.label}`);
        $('#variantQty').val(1).removeAttr('max');
        $('#variantStockDisplay').text('-');
        $('#variantPriceBox').hide();
        $('#variantResolvedPrice').val(0);

        $.get(urls.variants.replace('__id__', product.id))
            .done(function(res) {
                const v = res.data || {};
                variantCombinations = v.combinations || [];
                if (variantCombinations.length > 0) {
                    variantUiMode = 'combo';
                    $('#variantCombinationSection').show();
                    $('#variantLegacySection').hide();
                    renderCombinationPickers(variantCombinations);
                } else {
                    variantUiMode = 'legacy';
                    $('#variantCombinationSection').hide();
                    $('#variantLegacySection').show();
                    renderLegacyVariantPickers(v);
                }
                variantModal.show();
                wireVariantModalAfterLoad();
                checkVariantStock();
            })
            .fail(function() {
                Swal.fire({ icon: 'error', title: 'Variants unavailable', text: 'Could not load variant options. Try again.' });
            });
    }

    $('#variantAddBtn').on('click', function() {
        const productId = Number($('#variantProductId').val());
        const product = productCache[productId];
        if (!product) return;
        let qty = Math.max(1, parseInt($('#variantQty').val(), 10) || 1);
        const resolvedPrice = parseFloat($('#variantResolvedPrice').val()) || Number(product.selling_price || 0);
        const unitPrice = resolvedPrice > 0 ? resolvedPrice : Number(product.selling_price || 0);

        if (variantUiMode === 'combo') {
            const $pick = $('input[name="variant_combo_pick"]:checked');
            if (!$pick.length) return;
            const pvid = Number($pick.val());
            const combo = (variantCombinations || []).find(c => Number(c.product_variant_id) === pvid);
            if (!combo) return;
            const maxStock = parseInt($pick.data('stock'), 10) || 0;
            if (qty > maxStock) {
                qty = maxStock;
                $('#variantQty').val(qty);
            }
            if (qty < 1) {
                Swal.fire({ icon: 'warning', title: 'No stock', text: 'Select a SKU with available quantity.' });
                return;
            }
            const key = `pv-${product.id}-${pvid}`;
            const existing = cart.find(x => x.key === key);
            if (existing) {
                const nextQty = existing.quantity + qty;
                existing.quantity = nextQty > maxStock ? maxStock : nextQty;
            } else {
                cart.push({
                    key,
                    product_id: product.id,
                    product_label: `${product.brand} - ${product.label}`,
                    variant_label: combo.label,
                    product_type: product.product_type,
                    quantity: qty,
                    unit_price: unitPrice,
                    discount_price: 0,
                    device_id: null,
                    imei: null,
                    product_variant_id: pvid,
                    ram_option_id: combo.ram_option_id || null,
                    storage_option_id: combo.storage_option_id || null,
                    color_option_id: combo.color_option_id || null
                });
            }
        } else {
            const ram_id = $('input[name="variant_ram"]:checked').val() || null;
            const ram_val = $('input[name="variant_ram"]:checked').data('label');
            const storage_id = $('input[name="variant_storage"]:checked').val() || null;
            const storage_val = $('input[name="variant_storage"]:checked').data('label');
            const color_id = $('input[name="variant_color"]:checked').val() || null;
            const color_val = $('input[name="variant_color"]:checked').data('label');
            const key = `p-${product.id}-${ram_id || 'any'}-${storage_id || 'any'}-${color_id || 'any'}`;
            const existing = cart.find(x => x.key === key);
            const optionsLabel = [ram_id ? ram_val : null, storage_id ? storage_val : null, color_id ? color_val : null].filter(Boolean).join(' / ');
            const labelSuffix = optionsLabel ? ` (${optionsLabel})` : '';
            if (existing) {
                existing.quantity += qty;
            } else {
                cart.push({
                    key,
                    product_id: product.id,
                    product_label: `${product.brand} - ${product.label}` + labelSuffix,
                    variant_label: optionsLabel || null,
                    product_type: product.product_type,
                    quantity: qty,
                    unit_price: unitPrice,
                    discount_price: 0,
                    device_id: null,
                    imei: null,
                    product_variant_id: null,
                    ram_option_id: ram_id,
                    storage_option_id: storage_id,
                    color_option_id: color_id
                });
            }
        }
        variantModal.hide();
        renderCart();
    });

    // ── Checkout ──────────────────────────────────────────────────────
    $('#checkoutBtn').on('click', function() {
        // Validate trade-in fields
        if (!$('#ti_phone_model').val()) { Swal.fire({ icon: 'warning', title: 'Trade-In Required', text: 'Please select the customer\'s trade-in phone model.' }); return; }
        if (!$('#ti_imei').val().trim()) { Swal.fire({ icon: 'warning', title: 'IMEI Required', text: 'Please enter the trade-in phone IMEI.' }); return; }
        if (!cart.length) { Swal.fire({ icon: 'warning', title: 'Empty Cart', text: 'Please add at least one product to the cart.' }); return; }

        const formData = new FormData();
        formData.append('_token', csrf);
        formData.append('tradein_phone_model_id', $('#ti_phone_model').val());
        formData.append('tradein_imei', $('#ti_imei').val().trim());
        formData.append('tradein_ram', $('#ti_ram').val() || '');
        formData.append('tradein_storage', $('#ti_storage').val() || '');
        formData.append('tradein_color', $('#ti_color_hidden').val() || $('#ti_color_select').val() || '');
        formData.append('tradein_condition', $('#ti_condition').val());
        formData.append('tradein_battery', $('#ti_battery').val());
        formData.append('tradein_buy_price', $('#ti_buy_price').val());
        formData.append('tradein_purchase_at', $('#ti_purchase_at').val() || '');

        // Trade-in images (base64)
        $('.ti-img-hidden').each(function() { formData.append('tradein_images[]', $(this).val()); });

        // Customer: existing (by ID) or new (by name fields)
        const existingCustomerId = $('#customerId').val();
        if (existingCustomerId) {
            formData.append('customer_id', existingCustomerId);
        } else {
            formData.append('customer_name',    $('#customerName').val() || '');
            formData.append('customer_phone',   $('#customerPhone').val() || '');
            formData.append('customer_nrc',     $('#customerNRC').val() || '');
            formData.append('customer_address', $('#customerAddress').val() || '');
        }
        formData.append('payment_type', $('input[name="paymentType"]:checked').val());

        if ($('input[name="paymentType"]:checked').val() === 'installment') {
            formData.append('installment_rate_id', $('#installmentRate').val());
            formData.append('down_payment', $('#downPayment').val());
        }

        cart.forEach((line, idx) => {
            formData.append(`items[${idx}][product_id]`, line.product_id);
            formData.append(`items[${idx}][quantity]`,   line.quantity);
            formData.append(`items[${idx}][unit_price]`, line.unit_price);
            formData.append(`items[${idx}][discount_price]`, line.discount_price);
            if (line.device_id) formData.append(`items[${idx}][device_id]`, line.device_id);
            if (line.imei)      formData.append(`items[${idx}][imei]`,      line.imei);
            if (!line.device_id && line.product_variant_id) {
                formData.append(`items[${idx}][product_variant_id]`, line.product_variant_id);
            }
            if (line.ram_option_id)     formData.append(`items[${idx}][ram_option_id]`,     line.ram_option_id);
            if (line.storage_option_id) formData.append(`items[${idx}][storage_option_id]`, line.storage_option_id);
            if (line.color_option_id)   formData.append(`items[${idx}][color_option_id]`,   line.color_option_id);
        });

        const $btn = $('#checkoutBtn').prop('disabled', true).text('Processing…');
        $.ajax({ url: urls.checkout, method: 'POST', data: formData, processData: false, contentType: false })
            .done(function(res) {
                if (res.status && res.receipt_url) {
                    const receiptUrl = res.receipt_url + (res.receipt_url.indexOf('?') >= 0 ? '&' : '?') + 'print=1';
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Trade-in complete — opening receipt…',
                        showConfirmButton: false,
                        timer: 1400,
                        timerProgressBar: true,
                    }).then(function() {
                        window.location.href = receiptUrl;
                    });
                }
            })
            .fail(function(xhr) {
                $btn.prop('disabled', false).html('<i class="fas fa-check me-1"></i> Checkout Trade-In Sale');
                let msg = xhr.responseJSON?.message || 'Something went wrong.';
                if (xhr.responseJSON?.errors) msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                Swal.fire({ icon: 'error', title: 'Error', html: msg });
            });
    });

    // Auto-load on page load
    loadProducts('');

    // Prevent default drag behaviour on images to avoid browser opening them
    $(document).on('dragstart', 'img', function(e) { e.preventDefault(); });
});
</script>
@endsection
