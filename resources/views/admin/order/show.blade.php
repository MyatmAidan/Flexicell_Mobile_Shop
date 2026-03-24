@extends('layouts.app')

@section('meta')
    <meta name="description" content="Order Detail">
@endsection

@section('title', 'Order #' . str_pad($order->id, 5, '0', STR_PAD_LEFT))

@section('style')
<style>
    .info-label { font-size: 0.78rem; color: #6c757d; text-transform: uppercase; letter-spacing: 0.04em; }
    .info-value { font-size: 1rem; font-weight: 600; }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12 mb-3 d-flex justify-content-between align-items-center">
        <a href="{{ route('admin.order.index') }}" class="btn btn-md btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Orders
        </a>
        <a href="{{ route('admin.order.receipt', $order->id) }}" target="_blank" class="btn btn-md btn-primary">
            <i class="fas fa-print me-1"></i> Print Receipt
        </a>
    </div>

    {{-- Order Summary --}}
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    Order #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                    @php
                        $statusColors = ['completed' => 'success', 'installment' => 'warning', 'pending' => 'secondary', 'cancelled' => 'danger'];
                        $color = $statusColors[$order->order_status] ?? 'secondary';
                    @endphp
                    <span class="badge bg-{{ $color }} ms-2">{{ ucfirst($order->order_status) }}</span>
                </h5>
                <div class="row g-3 mb-4">
                    <div class="col-sm-4">
                        <div class="info-label">Customer</div>
                        <div class="info-value">
                            {{ $order->customer?->name ?? 'Walk-in' }}
                            @if($order->customer?->phone)
                                <br><small class="text-muted">{{ $order->customer->phone }}</small>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="info-label">Address / Shipping</div>
                        <div class="info-value">{{ $order->customer?->address ?? ($order->shipping_address ?: '-') }}</div>
                    </div>
                    @if($order->customer?->attachments)
                    <div class="col-sm-4">
                        <div class="info-label">Attachments</div>
                        <div class="info-value mt-1">
                            @foreach(($order->customer?->attachments ?? []) as $path)
                                <a href="{{ asset('storage/' . $path) }}" target="_blank" class="btn btn-outline-info btn-md py-0 px-1 mb-1" style="font-size: 1rem;">
                                    <i class="fas fa-file-download"></i> View
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    <div class="col-sm-4">
                        <div class="info-label">Order Date</div>
                        <div class="info-value">{{ $order->order_date ? $order->order_date->format('d M Y, h:i A') : '-' }}</div>
                    </div>
                    <div class="col-sm-4">
                        <div class="info-label">Delivered At</div>
                        <div class="info-value">{{ $order->delivered_at ? $order->delivered_at->format('d M Y') : '-' }}</div>
                    </div>
                </div>

                <h6 class="mb-3">Order Items</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>IMEI / Device</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Discount</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $i => $item)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    <span class="fw-semibold">{{ $item->product?->phoneModel?->model_name ?? '-' }}</span><br>
                                    <small class="text-muted">{{ $item->product?->phoneModel?->brand?->brand_name ?? '' }} &mdash; {{ $item->product?->product_type ?? '' }}</small>
                                </td>
                                <td>{{ $item->device?->imei ?? '-' }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">{{ number_format($item->unit_price) }} Ks</td>
                                <td class="text-end text-danger">{{ $item->discount_price > 0 ? '-' . number_format($item->discount_price) . ' Ks' : '-' }}</td>
                                <td class="text-end fw-semibold">{{ number_format($item->total) }} Ks</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Totals & Installment Summary --}}
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="card-title">Payment Summary</h6>
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted">Subtotal</td>
                        <td class="text-end">{{ number_format($order->total_amount) }} Ks</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Discount</td>
                        <td class="text-end text-danger">-{{ number_format($order->discount_amount) }} Ks</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tax</td>
                        <td class="text-end">{{ number_format($order->tax_amount) }} Ks</td>
                    </tr>
                    <tr class="fw-bold">
                        <td>Grand Total</td>
                        <td class="text-end">{{ number_format($order->grand_total) }} Ks</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    @if ($order->installment)
    <div class="col-md-6">
        <div class="card border-warning">
            <div class="card-header bg-warning bg-opacity-10 text-warning fw-semibold">
                <i class="fas fa-calendar-alt me-1"></i> Installment Plan
            </div>
            <div class="card-body">
                @php $inst = $order->installment; @endphp
                <table class="table table-sm mb-3">
                    <tr>
                        <td class="text-muted">Plan</td>
                        <td class="text-end">{{ $inst->months }} months @ {{ $inst->rate?->rate }}%</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Total (with interest)</td>
                        <td class="text-end">{{ number_format($inst->total_amount) }} Ks</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Down Payment</td>
                        <td class="text-end">{{ number_format($inst->down_payment) }} Ks</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Remaining</td>
                        <td class="text-end">{{ number_format($inst->remaining_amount) }} Ks</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Monthly</td>
                        <td class="text-end fw-semibold text-warning">{{ number_format($inst->monthly_amount) }} Ks</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Start Date</td>
                        <td class="text-end">{{ $inst->start_date ? $inst->start_date->format('d M Y') : '-' }}</td>
                    </tr>
                </table>
                <a href="{{ route('admin.installment.show', $inst->id) }}" class="btn btn-warning btn-md w-100">
                    <i class="fas fa-list me-1"></i> View Payment Schedule
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
