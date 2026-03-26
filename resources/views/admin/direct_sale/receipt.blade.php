@extends('layouts.app')

@section('title', 'Direct Sale Receipt')

@section('style')
<style>
    @media print {
        .no-print { display: none !important; }
        .page-wrapper, .container-fluid { padding: 0 !important; }
    }
    .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
</style>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <h4 class="mb-0">Receipt</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.direct_sale.index') }}" class="btn btn-secondary">Back to POS</a>
            <button class="btn btn-primary" onclick="window.print()">Print</button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h5 class="mb-1">Flexicell Mobile</h5>
                    <div class="text-muted">Direct Sale Receipt</div>
                </div>
                <div class="col-md-6 text-md-end">
                    <div><strong>Order #</strong> <span class="mono">{{ $order->id }}</span></div>
                    <div><strong>Date</strong> {{ optional($order->order_date)->format('Y-m-d H:i') }}</div>
                    <div><strong>Status</strong> {{ $order->order_status }}</div>
                </div>
            </div>

            <hr>

            <div class="row mb-3">
                <div class="col-md-6">
                    <h6 class="mb-2">Customer</h6>
                    @if ($order->customer)
                        <div><strong>Name:</strong> {{ $order->customer->name }}</div>
                        <div><strong>Phone:</strong> {{ $order->customer->phone ?? '-' }}</div>
                        <div><strong>NRC:</strong> {{ $order->customer->nrc ?? '-' }}</div>
                        <div><strong>Address:</strong> {{ $order->customer->address ?? '-' }}</div>
                    @else
                        <div class="text-muted">Walk-in customer</div>
                    @endif
                </div>
                <div class="col-md-6 text-md-end">
                    <h6 class="mb-2">Handled By</h6>
                    <div>{{ $order->user?->name ?? '-' }}</div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Variant</th>
                            <th>IMEI</th>
                            <th class="text-end">Unit</th>
                            <th class="text-end">Disc</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $i => $item)
                            @php
                                $pm = $item->product?->phoneModel;
                                $dev = $item->device;
                            @endphp
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $pm?->model_name ?? '-' }}</div>
                                    <div class="small text-muted">{{ $pm?->brand?->brand_name ?? '-' }} • {{ $item->product?->product_type }}</div>
                                </td>
                                <td class="small">
                                    {{ $dev?->ram ?? '-' }} / {{ $dev?->storage ?? '-' }} / {{ $dev?->color ?? '-' }}
                                </td>
                                <td class="mono small">{{ $dev?->imei ?? '-' }}</td>
                                <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end">{{ number_format($item->discount_price, 2) }}</td>
                                <td class="text-end fw-semibold">{{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h6 class="mb-2">Payments</h6>
                    @if ($order->payments->count())
                        <ul class="mb-0">
                            @foreach ($order->payments as $p)
                                <li>{{ $p->payment_type }} — {{ number_format($p->amount, 2) }} ({{ $p->status }})</li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-muted">No payment records.</div>
                    @endif

                    @if ($order->installment)
                        <hr>
                        <h6 class="mb-2">Installment</h6>
                        <div><strong>Plan:</strong> {{ $order->installment->months }} months @ {{ $order->installment->rate?->rate ?? '-' }}%</div>
                        <div><strong>Total w/ interest:</strong> {{ number_format($order->installment->total_amount, 2) }}</div>
                        <div><strong>Down payment:</strong> {{ number_format($order->installment->down_payment, 2) }}</div>
                        <div><strong>Monthly:</strong> {{ number_format($order->installment->monthly_amount, 2) }}</div>
                    @endif
                </div>
                <div class="col-md-6">
                    <table class="table table-sm">
                        <tr>
                            <td class="text-muted">Subtotal</td>
                            <td class="text-end">{{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Discount</td>
                            <td class="text-end">{{ number_format($order->discount_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Grand Total</td>
                            <td class="text-end fw-bold">{{ number_format($order->grand_total, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection

