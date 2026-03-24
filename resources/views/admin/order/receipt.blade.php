<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
            line-height: 1.2;
            margin: 0;
            padding: 10px;
            color: #000;
            background: #fff;
        }
        .receipt-container {
            max-width: 380px;
            margin: 0 auto;
            background: #fff;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .dashed-line {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .header h1 {
            margin: 0 0 5px 0;
            font-size: 26px;
            text-transform: uppercase;
        }
        .header p {
            margin: 2px 0;
            font-size: 14px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 4px 0;
        }
        .items-table {
            width: 100%;
            margin: 10px 0;
        }
        .items-table td {
            vertical-align: top;
            padding: 2px 0;
        }
        .totals-section {
            margin-top: 10px;
        }
        .payment-section {
            margin-top: 10px;
        }
        .footer {
            margin-top: 20px;
            text-transform: uppercase;
        }
        .barcode {
            margin-top: 15px;
            text-align: center;
        }
        /* Barcode placeholder logic */
        .barcode-bars {
            height: 40px;
            width: 100%;
            background: repeating-linear-gradient(
                90deg,
                #000,
                #000 1px,
                #fff 1px,
                #fff 3px,
                #000 3px,
                #000 4px,
                #fff 4px,
                #fff 6px
            );
            margin: 0 auto;
            max-width: 250px;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            .receipt-container { width: 100%; max-width: 100%; }
        }
        .btn-group {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 6px 14px;
            font-family: sans-serif;
            font-size: 13px;
            text-decoration: none;
            color: #fff;
            background: #333;
            border-radius: 3px;
            cursor: pointer;
            margin: 0 5px;
        }
    </style>
</head>
<body>

    <div class="no-print btn-group">
        <button onclick="window.print()" class="btn">PRINT</button>
        <a href="{{ route('admin.direct_sale.index') }}" class="btn">NEW SALE</a>
        <a href="{{ route('admin.order.show', $order->id) }}" class="btn">ORDER DETAILS</a>
    </div>

    <div class="receipt-container">
        <!-- Logo/Store Info -->
        <div class="header text-center">
            <h1>Flexicell</h1>
            <p>The helpful place.</p>
            <p>Thank You for Shopping at</p>
            <p>Flexicell Mobile Shop</p>
            <p>Mandalay, Myanmar</p>
            <p>Phone: 09-768768464</p>
        </div>

        <div class="dashed-line"></div>

        <div class="info">
            <div class="info-row">
                <span>Date:</span>
                <span>{{ $order->order_date ? $order->order_date->format('m/d/Y, h:i:s A') : date('m/d/Y, h:i:s A') }}</span>
            </div>
            <div class="info-row">
                <span>Order ID:</span>
                <span>#{{ str_pad($order->id, 7, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="info-row">
                <span>Customer:</span>
                <span>{{ $order->customer->name ?? 'Walk-in' }}</span>
            </div>
            @if($order->customer?->phone)
            <div class="info-row">
                <span>Phone:</span>
                <span>{{ $order->customer->phone }}</span>
            </div>
            @endif
        </div>

        <div class="dashed-line"></div>

        <!-- Items -->
        <table class="items-table">
            @foreach ($order->items as $item)
            <tr>
                <td colspan="2">
                    # {{ $item->product_id }} &nbsp; 
                    {{ $item->product?->phoneModel?->model_name ?? 'Product' }}
                    @if($item->device?->imei)
                        <br>&nbsp;&nbsp;IMEI: {{ $item->device->imei }}
                    @endif
                </td>
                <td class="text-right">MMK{{ number_format($item->unit_price, 2) }}</td>
            </tr>
            @endforeach
        </table>

        <div class="dashed-line"></div>

        <!-- Totals -->
        <div class="totals-section">
            <div class="info-row">
                <span style="margin-left: 20%;">Subtotal</span>
                <span>MMK {{ number_format((float)($order->total_amount ?? 0), 2) }}</span>
            </div>
            @if($order->discount_amount > 0)
            <div class="info-row">
                <span style="margin-left: 20%;">Discount</span>
                <span>-MMK {{ number_format((float)($order->discount_amount ?? 0), 2) }}</span>
            </div>
            @endif
            <div class="info-row">
                <span style="margin-left: 20%;">Tax</span>
                <span>MMK 0.00</span>
            </div>
            <div class="info-row fw-bold" style="margin-top: 10px; font-size: 16px;">
                <span style="margin-left: 20%;">Total</span>
                <span>MMK {{ number_format((float)($order->grand_total ?? 0), 2) }}</span>
            </div>
        </div>

        <div class="dashed-line"></div>

        <!-- Payment Details -->
        <div class="payment-section">
            @if($order->installment)
                <div class="info-row">
                    <span>Payment Type</span>
                    <span class="text-right">INSTALLMENT</span>
                </div>
                <div class="info-row">
                    <span>Months</span>
                    <span class="text-right">{{ $order->installment->months }}</span>
                </div>
                <div class="info-row">
                    <span>Monthly</span>
                    <span class="text-right">MMK {{ number_format((float)($order->installment->monthly_amount ?? 0), 2) }}</span>
                </div>
                <div class="info-row">
                    <span>Down Payment</span>
                    <span class="text-right">MMK {{ number_format((float)($order->installment->down_payment ?? 0), 2) }}</span>
                </div>
            @else
                <div class="info-row">
                    <span>Payment Type</span>
                    <span class="text-right">CASH</span>
                </div>
                <div class="info-row">
                    <span>Status</span>
                    <span class="text-right">PAID</span>
                </div>
                <div class="info-row">
                    <span>Reference #</span>
                    <span class="text-right">{{ strtoupper(uniqid()) }}</span>
                </div>
            @endif
        </div>

        <div class="dashed-line"></div>

        <!-- Footer -->
        <div class="footer text-center">
            <p>THANK YOU</p>
            <p>HAVE A NICE DAY</p>
        </div>

        <!-- Barcode Placeholder -->
        <div class="barcode">
            <div class="barcode-bars"></div>
            <div style="margin-top: 5px;">{{ str_pad($order->id, 12, '0', STR_PAD_LEFT) }}</div>
        </div>
    </div>

    <script>
        // Auto print if requested via query param
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('print')) {
            window.onload = function() {
                window.print();
            }
        }
    </script>
</body>
</html>
