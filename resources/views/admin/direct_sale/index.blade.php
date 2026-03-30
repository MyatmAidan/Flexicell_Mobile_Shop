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
    .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }

    /* Product card base */
    .pos-product-item { cursor: grab; transition: transform 0.15s, box-shadow 0.15s; border: 1px solid #e8eaed; border-radius: 8px; overflow: hidden; }
    .pos-product-item:hover { transform: translateY(-2px); box-shadow: 0 4px 14px rgba(0,0,0,0.1) !important; }
    .pos-product-item.dragging { opacity: 0.5; transform: scale(0.95); }
    .pos-product-item .card-body { padding: 0 !important; }

    /* Product image area (grid) */
    .pos-product-item .product-thumb {
        width: 100%; height: 90px;
        background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%);
        display: flex; align-items: center; justify-content: center;
        overflow: hidden; position: relative;
    }
    .pos-product-item .product-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .pos-product-item .product-thumb .thumb-icon { font-size: 2rem; color: #b0bec5; }
    .pos-product-item .product-meta { padding: 10px 12px; }
    .pos-product-item .product-meta .product-name { font-size: 0.82rem; font-weight: 600; line-height: 1.3; margin-bottom: 6px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .pos-product-item .product-meta .product-footer { display: flex; justify-content: space-between; align-items: center; }
    .pos-product-item .product-meta .stock-badge { font-size: 0.72rem; color: #64748b; }
    .pos-product-item .product-meta .stock-badge span { font-weight: 700; color: #334155; }

    /* View mode toggle */
    .view-toggle .btn { padding: 4px 10px; font-size: 0.85rem; }
    .view-toggle .btn.active { background: #0d6efd; color: #fff; border-color: #0d6efd; }

    /* Grid small view */
    .view-grid-sm .pos-product-item .product-thumb { height: 75px; }
    .view-grid-sm .pos-product-item .product-thumb .thumb-icon { font-size: 1.4rem; }
    .view-grid-sm .pos-product-item .product-meta { padding: 6px 8px; }
    .view-grid-sm .pos-product-item .product-meta .product-name { font-size: 0.72rem; margin-bottom: 4px; }
    .view-grid-sm .pos-product-item .product-meta .stock-badge { font-size: 0.65rem; }
    .view-grid-sm .pos-product-item .product-meta .btn { padding: 2px 6px; font-size: 0.7rem; }

    /* List view */
    .view-list .pos-product-item .card-body { display: flex; align-items: center; gap: 12px; padding: 8px 12px !important; }
    .view-list .pos-product-item .product-thumb { width: 48px; height: 48px; min-width: 48px; border-radius: 8px; }
    .view-list .pos-product-item .product-thumb .thumb-icon { font-size: 1.2rem; }
    .view-list .pos-product-item .product-meta { padding: 0; flex: 1; min-width: 0; }
    .view-list .pos-product-item .product-meta .product-name { font-size: 0.82rem; margin-bottom: 0; -webkit-line-clamp: 1; }
    .view-list .pos-product-item .product-meta .product-footer { gap: 8px; }

    /* Cart drop zone highlight */
    .cart-drop-highlight { background-color: #e8f5e9 !important; border: 2px dashed #4caf50 !important; transition: all 0.2s; }

    /* Variant Selection Styling */
    .variant-group-label { font-size: 0.85rem; font-weight: 600; color: #666; margin-bottom: 8px; display: block; }
    .variant-tiles { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
    
    /* Radio Tile */
    .variant-tile-input { display: none; }
    .variant-tile-label {
        padding: 8px 16px;
        border: 2px solid #edeff2;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.9rem;
        transition: all 0.2s;
        background: #fff;
        user-select: none;
        min-width: 60px;
        text-align: center;
    }
    .variant-tile-input:checked + .variant-tile-label {
        border-color: #0d6efd;
        background-color: #f0f7ff;
        color: #0d6efd;
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(13, 110, 253, 0.1);
    }
    .variant-tile-label:hover { border-color: #cbd3da; }

    /* Color Swatch */
    .color-swatches { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; }
    .color-swatch-input { display: none; }
    .color-swatch-label {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        cursor: pointer;
        position: relative;
        transition: all 0.2s;
        border: 2px solid #edeff2;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .color-swatch-input:checked + .color-swatch-label {
        border-color: #0d6efd;
        transform: scale(1.1);
        box-shadow: 0 0 0 2px #fff, 0 0 0 4px #0d6efd;
    }
    .color-swatch-label .check-mark {
        color: #fff;
        font-size: 14px;
        display: none;
        text-shadow: 0 1px 2px rgba(0,0,0,0.5);
    }
    .color-swatch-input:checked + .color-swatch-label .check-mark { display: block; }
    
    #modalVariantImg {
        width: 100%;
        height: 200px;
        object-fit: contain;
        background: #f8f9fa;
        border-radius: 12px;
        margin-bottom: 15px;
    }
</style>
@endsection

@section('content')
    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <div class="card pos-panel">
                <div class="card-body">
                    <h5 class="mb-3">Direct Sale For All Products</h5>

                    <div class="row mb-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label">Search product</label>
                            <input type="text" id="productSearch" class="form-control" placeholder="Search by model name or type...">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Quick IMEI</label>
                            <div class="input-group">
                                <input type="text" id="imeiInput" class="form-control mono" placeholder="Scan / type IMEI">
                                <button class="btn btn-primary" id="imeiAddBtn" type="button">Add</button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="btn-group view-toggle w-100" role="group">
                                <button type="button" class="btn btn-outline-secondary active" data-view="grid" title="Grid"><i class="fas fa-th-large"></i></button>
                                <button type="button" class="btn btn-outline-secondary" data-view="grid-sm" title="Grid Small"><i class="fas fa-th"></i></button>
                                <button type="button" class="btn btn-outline-secondary" data-view="list" title="List"><i class="fas fa-list"></i></button>
                            </div>
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

                    <div class="mb-3">
                        <label class="form-label">Select Customer</label>
                        <div class="input-group">
                            <input type="text" id="customerSearchInput" class="form-control" placeholder="Search by name, phone, email..." autocomplete="off">
                            <input type="hidden" id="customerId" value="">
                        </div>
                        <div id="customerDropdown" class="border rounded mt-1 bg-white position-absolute shadow-sm" style="display:none; z-index:1050; max-height:200px; overflow-y:auto; width:calc(100% - 2rem);"></div>
                        <div id="customerSelected" class="small mt-1 text-success fw-semibold" style="display:none;"></div>
                    </div>

                    <div id="customerExtendedInfo" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label">Customer NRC</label>
                            <input type="text" id="customerNRC" class="form-control" placeholder="Required for installments">
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

    <!-- Variant Modal -->
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
                        <img id="modalVariantImg" src="" alt="Product Image" onerror="this.src='https://via.placeholder.com/300?text=No+Photo'">
                        <h5 class="fw-bold mb-0" id="variantProductName"></h5>
                    </div>

                    <div class="mb-3">
                        <label class="variant-group-label">Select RAM</label>
                        <div class="variant-tiles" id="variantRamList"></div>
                    </div>

                    <div class="mb-3">
                        <label class="variant-group-label">Select Storage</label>
                        <div class="variant-tiles" id="variantStorageList"></div>
                    </div>

                    <div class="mb-3">
                        <label class="variant-group-label d-flex justify-content-between align-items-center">
                            <span>Select Color</span>
                            <span id="selectedColorName" class="fw-bold px-2 py-1 rounded" style="font-size: 0.8rem; background-color: #eee; color: #333;">Any</span>                        </label>
                        <div class="color-swatches" id="variantColorList"></div>
                    </div>

                    <div class="row align-items-center g-3">
                        <div class="col-6">
                            <label class="variant-group-label mb-0">Quantity</label>
                            <input type="number" class="form-control" id="variantQty" min="1" value="1">
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded bg-light" id="variantStockStatusBox">
                                <span class="d-block text-muted" style="font-size: 0.7rem;">AVAILABILITY</span>
                                <span id="variantStockDisplay" class="fw-bold">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 p-3 border rounded bg-light" id="variantPriceBox" style="display:none;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted fw-semibold" style="font-size: 0.85rem;">SELLING PRICE</span>
                            <span id="variantPriceDisplay" class="fw-bold fs-5 text-primary">-</span>
                        </div>
                        <input type="hidden" id="variantResolvedPrice" value="0">
                    </div>

                    {{-- <div class="alert alert-info py-2 px-3 mt-3 mb-0" style="font-size: 0.8rem;">
                        <i class="fas fa-info-circle me-1"></i> Only devices with valid IMEI are allocated.
                    </div> --}}
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
    $(function() {
        const csrf = $('meta[name="csrf-token"]').attr('content');
        const urls = {
            products: "{{ route('admin.direct_sale.products') }}",
            devices: "{{ route('admin.direct_sale.devices') }}",
            customers: "{{ route('admin.direct_sale.customers') }}",
            variants: "{{ route('admin.direct_sale.variants', ['product' => '__id__']) }}",
            variants_stock: "{{ route('admin.direct_sale.variants_stock') }}",
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
        let currentView = 'grid';
        const variantModal = new bootstrap.Modal(document.getElementById('variantModal'));

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

        const renderSwatches = (container, items) => {
            const $c = $(container).empty();
            $c.append(`
                <div class="color-swatch">
                    <input type="radio" name="variant_color" id="color_any" value="" class="color-swatch-input variant-option" checked>
                    <label for="color_any" class="color-swatch-label" style="background:#eee; color:#666;" title="Any">
                        <i class="fas fa-ban fa-xs"></i>
                    </label>
                </div>
            `);
            (items || []).forEach(x => {
                $c.append(`
                    <div class="color-swatch">
                        <input 
                            type="radio" 
                            name="variant_color" 
                            id="color_${x.id}" 
                            value="${x.id}" 
                            data-label="${x.name}" 
                            data-value="${x.value}" 
                            class="color-swatch-input variant-option"
                        >
                        <label for="color_${x.id}" class="color-swatch-label" style="background-color: ${x.value};" title="${x.name}">
                            <i class="fas fa-check check-mark"></i>
                        </label>
                    </div>
                `);
            });
        };

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
                    ram: null,
                    storage: null,
                    color: null,
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
                    renderProducts(items);
                })
                .fail(function() {
                    $('#productResults').html('<div class="text-danger small">Failed to load products.</div>');
                });
        }

        function renderProducts(items) {
            const view = currentView;
            const $container = $('#productResults');

            const cards = items.map(p => {
                const isNew = p.product_type === 'new';
                const stockText = isNew
                    ? `Stock: <span>${p.stock_quantity}</span>`
                    : `Devices: <span>${p.available_devices}</span>`;
                const addBtn = isNew
                    ? `<button class="btn btn-sm btn-primary add-qty-btn" data-product-id="${p.id}"><i class="fas fa-plus"></i></button>`
                    : `<span class="badge bg-warning">IMEI</span>`;
                const thumbContent = p.image
                    ? `<img src="${productImageBase}/${p.image}" alt="">`
                    : `<i class="fas fa-mobile-alt thumb-icon"></i>`;

                if (view === 'list') {
                    return `
                        <div class="mb-1">
                            <div class="card shadow-sm pos-product-item" draggable="true" data-product-id="${p.id}">
                                <div class="card-body">
                                    <div class="product-thumb">${thumbContent}</div>
                                    <div class="product-meta">
                                        <div class="product-name">${p.brand} - ${p.label}</div>
                                        <div class="product-footer">
                                            <div class="stock-badge">${stockText}</div>
                                            <div>${addBtn}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                }

                const colClass = view === 'grid-sm' ? 'col-4 col-md-3 col-xl-2' : 'col-6 col-md-4 col-xl-3';

                return `
                    <div class="${colClass} mb-2">
                        <div class="card h-100 shadow-sm pos-product-item" draggable="true" data-product-id="${p.id}">
                            <div class="card-body">
                                <div class="product-thumb">${thumbContent}</div>
                                <div class="product-meta">
                                    <div class="product-name">${p.brand} - ${p.label}</div>
                                    <div class="product-footer">
                                        <div class="stock-badge">${stockText}</div>
                                        <div>${addBtn}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;
            }).join('');

            const viewClass = view === 'list' ? 'view-list' : (view === 'grid-sm' ? 'view-grid-sm' : '');
            const wrapper = view === 'list' ? cards : `<div class="row g-2">${cards}</div>`;
            $container.html(`<div class="${viewClass}">${wrapper}</div>`);
            initDragSource();
        }

        $(document).on('click', '.add-qty-btn', function() {
            const id = Number($(this).data('product-id'));
            const product = productCache[id];
            if (!product) return;
            if (product.stock_quantity <= 0) {
                Swal.fire({ icon: 'warning', title: 'Out of stock', text: 'This product has no stock.' });
                return;
            }
            
            const imgUrl = product.image ? `${productImageBase}/${product.image}` : 'https://via.placeholder.com/300?text=No+Photo';
            $('#modalVariantImg').attr('src', imgUrl);
            $('#variantProductId').val(product.id);
            $('#variantProductName').text(`${product.brand} - ${product.label}`);
            $('#variantQty').val(1);
            $('#variantPriceBox').hide();
            $('#variantResolvedPrice').val(0);

            // Fetch variants and populate radios
            $.get(urls.variants.replace('__id__', product.id))
                .done(function(res) {
                    const v = res.data;
                    
                    const renderTiles = (container, items, name) => {
                        const $c = $(container).empty();
                        // Add "Any" option
                        $c.append(`
                            <div class="variant-tile shadow-sm">
                                <input type="radio" name="${name}" id="${name}_any" value="" class="variant-tile-input variant-option" checked>
                                <label for="${name}_any" class="variant-tile-label">Any</label>
                            </div>
                        `);
                        (items || []).forEach(x => {
                            $c.append(`
                                <div class="variant-tile shadow-sm">
                                    <input type="radio" name="${name}" id="${name}_${x.id}" value="${x.id}" data-label="${x.name}" class="variant-tile-input variant-option">
                                    <label for="${name}_${x.id}" class="variant-tile-label">${x.name}</label>
                                </div>
                            `);
                        });
                    };

                    const renderSwatches = (container, items) => {
                        const $c = $(container).empty();
                        $c.append(`
                            <div class="color-swatch">
                                <input type="radio" name="variant_color" id="color_any" value="" class="color-swatch-input variant-option" checked>
                                <label for="color_any" class="color-swatch-label" style="background:#eee; color:#666;" title="Any">
                                    <i class="fas fa-ban fa-xs"></i>
                                </label>
                            </div>
                        `);
                        (items || []).forEach(x => {
                            $c.append(`
                                <div class="color-swatch">
                                    <input type="radio" name="variant_color" id="color_${x.id}" value="${x.id}" data-label="${x.name}" class="color-swatch-input variant-option">
                                    <label for="color_${x.id}" class="color-swatch-label" style="background-color: ${x.value};" title="${x.name}">
                                        <i class="fas fa-check check-mark"></i>
                                    </label>
                                </div>
                            `);
                        });
                    };

                    renderTiles('#variantRamList', v.ram, 'variant_ram');
                    renderTiles('#variantStorageList', v.storage, 'variant_storage');
                    renderSwatches('#variantColorList', v.color);

                    variantModal.show();
                    checkVariantStock(); 

                    // Attach change listener to new radios
                    $('.variant-option').on('change', function() {
                        checkVariantStock();
                    });
                })
                .fail(function() {
                    upsertQtyProduct(product, 1);
                });
        });

        // Dynamic stock check in modal
        $('.variant-option').on('change', function() {
            checkVariantStock();
        });

        function checkVariantStock() {
            const productId = $('#variantProductId').val();
            const ram = $('input[name="variant_ram"]:checked').val();
            const storage = $('input[name="variant_storage"]:checked').val();
            const color = $('input[name="variant_color"]:checked').val();
            const colorName = $('input[name="variant_color"]:checked').data('label') || 'Any';
            const allSelected = ram && storage && color;

            $('#selectedColorName').text(colorName);
            $('#variantStockDisplay').html('<span class="spinner-border spinner-border-sm text-secondary"></span>');
            $('#variantAddBtn').prop('disabled', true);

            if (!allSelected) {
                $('#variantPriceBox').hide();
                $('#variantResolvedPrice').val(0);
            }

            $.get(urls.variants_stock, { product_id: productId, ram, storage, color })
                .done(function(res) {
                    const count = res.count || 0;
                    if (count > 0) {
                        $('#variantStockDisplay').html(`<span class="text-success">${count} Available</span>`);
                        $('#variantAddBtn').prop('disabled', !allSelected);
                    } else {
                        $('#variantStockDisplay').html('<span class="text-danger">Out of Stock</span>');
                        $('#variantAddBtn').prop('disabled', true);
                    }

                    if (allSelected && res.min_price !== null) {
                        if (res.min_price === res.max_price) {
                            $('#variantPriceDisplay').text(money(res.min_price) + ' MMK');
                        } else {
                            $('#variantPriceDisplay').text(money(res.min_price) + ' ~ ' + money(res.max_price) + ' MMK');
                        }
                        $('#variantResolvedPrice').val(res.min_price);
                        $('#variantPriceBox').slideDown(200);
                    } else if (allSelected) {
                        const product = productCache[Number(productId)];
                        const fallback = product ? product.selling_price : 0;
                        $('#variantPriceDisplay').text(money(fallback) + ' MMK');
                        $('#variantResolvedPrice').val(fallback);
                        $('#variantPriceBox').slideDown(200);
                    }
                })
                .fail(function() {
                    $('#variantStockDisplay').html('<span class="text-muted">Error</span>');
                    $('#variantPriceBox').hide();
                    $('#variantAddBtn').prop('disabled', true);
                });
        }

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

        // Customer search
        let customerTimer;
        $('#customerSearchInput').on('input', function() {
            clearTimeout(customerTimer);
            const q = $(this).val().trim();
            $('#customerId').val('');
            $('#customerSelected').hide();
            if (q.length < 1) { $('#customerDropdown').hide(); return; }
            customerTimer = setTimeout(() => {
                $.get(urls.customers, { q })
                    .done(function(res) {
                        const items = res.data || [];
                        const $dd = $('#customerDropdown').empty();
                        if (items.length === 0) {
                            $dd.html('<div class="p-2 text-muted small">No customers found.</div>').show();
                            return;
                        }
                        items.forEach(c => {
                            $dd.append(`<div class="p-2 border-bottom customer-option" style="cursor:pointer;" data-id="${c.id}" data-name="${c.name}" data-phone="${c.phone || ''}" data-email="${c.email || ''}">
                                <div class="fw-semibold">${c.name}</div>
                                <div class="small text-muted">${c.phone || '-'} &middot; ${c.email || '-'}</div>
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
            $('#customerSelected').html(`<i class="fas fa-check-circle"></i> ${name} (${phone || 'no phone'})`).show();
            $('#customerDropdown').hide();
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#customerSearchInput, #customerDropdown').length) {
                $('#customerDropdown').hide();
            }
        });

        // Checkout
        $('#checkoutBtn').on('click', function() {
            if (cart.length === 0) {
                Swal.fire({ icon: 'warning', title: 'Empty cart', text: 'Please add items to cart.' });
                return;
            }

            const payment_type = $('input[name="paymentType"]:checked').val();
            const customer_id = $('#customerId').val();
            if (!customer_id) {
                Swal.fire({ icon: 'warning', title: 'Customer required', text: 'Please select a customer.' });
                return;
            }

            if (payment_type === 'installment' && !$('#customerNRC').val().trim()) {
                Swal.fire({ icon: 'warning', title: 'NRC required', text: 'NRC is required for installment sales.' });
                return;
            }
            if (payment_type === 'installment' && !$('#installmentRate').val()) {
                Swal.fire({ icon: 'warning', title: 'Missing plan', text: 'Please select an installment plan.' });
                return;
            }

            const formData = new FormData();

            cart.forEach((l, i) => {
                formData.append(`items[${i}][product_id]`, l.product_id);
                formData.append(`items[${i}][quantity]`, l.device_id ? 1 : l.quantity);
                if (l.device_id) formData.append(`items[${i}][device_id]`, l.device_id);
                if (l.imei) formData.append(`items[${i}][imei]`, l.imei);
                formData.append(`items[${i}][unit_price]`, Number(l.unit_price));
                formData.append(`items[${i}][discount_price]`, Number(l.discount_price || 0));
                if (!l.device_id) {
                    if (l.ram_option_id) formData.append(`items[${i}][ram_option_id]`, l.ram_option_id);
                    if (l.storage_option_id) formData.append(`items[${i}][storage_option_id]`, l.storage_option_id);
                    if (l.color_option_id) formData.append(`items[${i}][color_option_id]`, l.color_option_id);
                }
            });

            formData.append('customer_id', customer_id);
            formData.append('payment_type', payment_type);

            if (payment_type === 'installment') {
                formData.append('customer_nrc', $('#customerNRC').val());
                formData.append('installment_rate_id', $('#installmentRate').val() || '');
                formData.append('down_payment', Number($('#downPayment').val() || 0));

                const files = $('#customerAttachments')[0].files;
                for (let i = 0; i < files.length; i++) {
                    formData.append('attachments[]', files[i]);
                }
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
                    if (res.data?.receipt_url) {
                        window.location.href = res.data.receipt_url + '?print=1';
                        return;
                    }
                    window.location.href = "{{ route('admin.direct_sale.index') }}";
                },
                error: function(xhr) {
                    let msg = xhr.responseJSON?.message || 'Checkout failed.';
                    if (xhr.responseJSON?.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    }
                    Swal.fire({ icon: 'error', title: 'Error', text: msg });
                },
                complete: function() {
                    $('#checkoutBtn').prop('disabled', false).text('Checkout');
                }
            });
        });

        // View toggle
        $('.view-toggle .btn').on('click', function() {
            $('.view-toggle .btn').removeClass('active');
            $(this).addClass('active');
            currentView = $(this).data('view');
            const cached = Object.values(productCache);
            if (cached.length > 0) {
                renderProducts(cached);
            }
        });

        // Drag and drop
        function initDragSource() {
            document.querySelectorAll('.pos-product-item[draggable="true"]').forEach(el => {
                el.addEventListener('dragstart', function(e) {
                    const pid = this.dataset.productId;
                    e.dataTransfer.setData('text/plain', pid);
                    e.dataTransfer.effectAllowed = 'copy';
                    this.classList.add('dragging');
                });
                el.addEventListener('dragend', function() {
                    this.classList.remove('dragging');
                    $('#cartTable').closest('.card').removeClass('cart-drop-highlight');
                });
            });
        }

        const cartCard = document.querySelector('#cartTable')?.closest('.card');
        if (cartCard) {
            cartCard.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'copy';
                this.classList.add('cart-drop-highlight');
            });
            cartCard.addEventListener('dragleave', function(e) {
                if (!this.contains(e.relatedTarget)) {
                    this.classList.remove('cart-drop-highlight');
                }
            });
            cartCard.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('cart-drop-highlight');
                const pid = Number(e.dataTransfer.getData('text/plain'));
                const product = productCache[pid];
                if (!product) return;

                if (product.product_type !== 'new') {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: 'Use IMEI to add second-hand products', timer: 2000, showConfirmButton: false });
                    return;
                }
                if (product.stock_quantity <= 0) {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'warning', title: 'Out of stock', timer: 1500, showConfirmButton: false });
                    return;
                }

                // Open variant modal (same as clicking Add)
                const imgUrl = product.image ? `${productImageBase}/${product.image}` : 'https://via.placeholder.com/300?text=No+Photo';
                $('#modalVariantImg').attr('src', imgUrl);
                $('#variantProductId').val(product.id);
                $('#variantProductName').text(`${product.brand} - ${product.label}`);
                $('#variantQty').val(1);
                $('#variantPriceBox').hide();
                $('#variantResolvedPrice').val(0);

                $.get(urls.variants.replace('__id__', product.id))
                    .done(function(res) {
                        const v = res.data;
                        const renderTiles = (container, items, name) => {
                            const $c = $(container).empty();
                            $c.append(`<div class="variant-tile shadow-sm"><input type="radio" name="${name}" id="${name}_any" value="" class="variant-tile-input variant-option" checked><label for="${name}_any" class="variant-tile-label">Any</label></div>`);
                            (items || []).forEach(x => {
                                $c.append(`<div class="variant-tile shadow-sm"><input type="radio" name="${name}" id="${name}_${x.id}" value="${x.id}" data-label="${x.name}" class="variant-tile-input variant-option"><label for="${name}_${x.id}" class="variant-tile-label">${x.name}</label></div>`);
                            });
                        };
                        const renderSwatchesLocal = (container, items) => {
                            const $c = $(container).empty();
                            $c.append(`<div class="color-swatch"><input type="radio" name="variant_color" id="color_any" value="" class="color-swatch-input variant-option" checked><label for="color_any" class="color-swatch-label" style="background:#eee; color:#666;" title="Any"><i class="fas fa-ban fa-xs"></i></label></div>`);
                            (items || []).forEach(x => {
                                $c.append(`<div class="color-swatch"><input type="radio" name="variant_color" id="color_${x.id}" value="${x.id}" data-label="${x.name}" class="color-swatch-input variant-option"><label for="color_${x.id}" class="color-swatch-label" style="background-color: ${x.value};" title="${x.name}"><i class="fas fa-check check-mark"></i></label></div>`);
                            });
                        };
                        renderTiles('#variantRamList', v.ram, 'variant_ram');
                        renderTiles('#variantStorageList', v.storage, 'variant_storage');
                        renderSwatchesLocal('#variantColorList', v.color);
                        variantModal.show();
                        checkVariantStock();
                        $('.variant-option').on('change', function() { checkVariantStock(); });
                    })
                    .fail(function() {
                        upsertQtyProduct(product, 1);
                    });
            });
        }

        // Initial load
        loadProducts('');
        renderCart();
        $('#imeiInput').focus();

        // Variant modal add
        $('#variantAddBtn').on('click', function() {
            const productId = Number($('#variantProductId').val());
            const product = productCache[productId];
            if (!product) return;
            const qty = Math.max(1, Number($('#variantQty').val() || 1));
            
            const ram_id = $('input[name="variant_ram"]:checked').val() || null;
            const ram_val = $('input[name="variant_ram"]:checked').data('label');
            
            const storage_id = $('input[name="variant_storage"]:checked').val() || null;
            const storage_val = $('input[name="variant_storage"]:checked').data('label');
            
            const color_id = $('input[name="variant_color"]:checked').val() || null;
            const color_val = $('input[name="variant_color"]:checked').data('label');

            const resolvedPrice = Number($('#variantResolvedPrice').val() || 0);
            const unitPrice = resolvedPrice > 0 ? resolvedPrice : Number(product.selling_price || 0);

            const key = `p-${product.id}-${ram_id || 'any'}-${storage_id || 'any'}-${color_id || 'any'}`;
            const existing = cart.find(x => x.key === key);

            const optionsLabel = [
                ram_id ? ram_val : null,
                storage_id ? storage_val : null,
                color_id ? color_val : null
            ].filter(Boolean).join('/');
            
            const labelSuffix = optionsLabel ? ` (${optionsLabel})` : '';

            if (existing) {
                existing.quantity += qty;
            } else {
                cart.push({
                    key,
                    product_id: product.id,
                    product_label: product.label + labelSuffix,
                    unit_price: unitPrice,
                    discount_price: 0,
                    quantity: qty,
                    ram_option_id: ram_id,
                    storage_option_id: storage_id,
                    color_option_id: color_id,
                });
            }
            variantModal.hide();
            renderCart();
        });
    });
</script>
@endsection

