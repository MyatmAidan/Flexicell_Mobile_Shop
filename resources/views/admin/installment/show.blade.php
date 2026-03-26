@extends('layouts.app')

@section('meta')
    <meta name="description" content="Installment Detail">
@endsection

@section('title', 'Installment #' . str_pad($installment->id, 5, '0', STR_PAD_LEFT))

@section('style')
<style>
    .info-label { font-size: 0.78rem; color: #6c757d; text-transform: uppercase; letter-spacing: 0.04em; }
    .info-value { font-size: 1rem; font-weight: 600; }
    .payment-row-paid { background-color: rgba(25, 135, 84, 0.05); }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <a href="{{ route('admin.installment.index') }}" class="btn btn-md btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Installments
        </a>
    </div>

    {{-- Installment Summary --}}
    <div class="col-md-5">
        <div class="card mb-4">
            <div class="card-header bg-warning bg-opacity-10 text-warning font-weight-bold">
                <iconify-icon icon="solar:bill-list-bold-duotone" class="me-1"></iconify-icon>
                Installment #{{ str_pad($installment->id, 5, '0', STR_PAD_LEFT) }}
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted">Order</td>
                        <td class="text-end">
                            <a href="{{ route('admin.order.show', $installment->order_id) }}" class="fw-semibold text-primary">
                                #{{ str_pad($installment->order_id, 5, '0', STR_PAD_LEFT) }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Customer</td>
                        <td class="text-end">{{ $installment->order?->shipping_address ?: 'Walk-in' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Plan</td>
                        <td class="text-end">{{ $installment->months }} months @ {{ $installment->rate?->rate }}%</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Total (with interest)</td>
                        <td class="text-end fw-semibold">{{ number_format($installment->total_amount) }} Ks</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Down Payment</td>
                        <td class="text-end">{{ number_format($installment->down_payment) }} Ks</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Remaining Amount</td>
                        <td class="text-end">{{ number_format($installment->remaining_amount) }} Ks</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Monthly Payment</td>
                        <td class="text-end fw-bold text-warning">{{ number_format($installment->monthly_amount) }} Ks</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Start Date</td>
                        <td class="text-end">{{ $installment->start_date ? $installment->start_date->format('d M Y') : '-' }}</td>
                    </tr>
                </table>

                @php
                    $paidCount = $installment->payments->where('status', 'paid')->count();
                    $totalCount = $installment->payments->count();
                    $paidAmount = $installment->payments->where('status', 'paid')->sum('paid_amount');
                    $pendingAmount = $installment->payments->where('status', 'pending')->sum('paid_amount');
                @endphp

                <hr>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted small">Payment Progress</span>
                    <span class="small fw-semibold">{{ $paidCount }} / {{ $totalCount }}</span>
                </div>
                <div class="progress mb-2" style="height: 8px;">
                    <div class="progress-bar bg-success" style="width: {{ $totalCount > 0 ? ($paidCount / $totalCount) * 100 : 0 }}%"></div>
                </div>
                <div class="d-flex justify-content-between small text-muted">
                    <span>Paid: {{ number_format($paidAmount) }} Ks</span>
                    <span>Remaining: {{ number_format($pendingAmount) }} Ks</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Schedule --}}
    <div class="col-md-7">
        <div class="card mb-4">
            <div class="card-header bg-warning bg-opacity-10 text-warning font-weight-bold">
                <i class="fas fa-calendar-alt me-1"></i> Payment Schedule
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Due Date</th>
                                <th class="text-end">Amount</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $downPayment = $installment->payments->where('status', 'paid')->where('paid_amount', $installment->down_payment)->first();
                                $monthlyPayments = $installment->payments
                                    ->reject(fn($p) => $p->id === optional($downPayment)->id && $p->status === 'paid' && $p->paid_amount == $installment->down_payment && $installment->down_payment > 0)
                                    ->sortBy('paid_date')
                                    ->values();
                            @endphp

                            @if ($installment->down_payment > 0 && $downPayment)
                            <tr class="payment-row-paid table-success">
                                <td class="ps-3"><span class="badge bg-secondary">DP</span></td>
                                <td>{{ $downPayment->paid_date ? \Carbon\Carbon::parse($downPayment->paid_date)->format('d M Y') : '-' }}</td>
                                <td class="text-end">{{ number_format($downPayment->paid_amount) }} Ks</td>
                                <td class="text-center"><span class="badge bg-success">Paid</span></td>
                                <td class="text-center">-</td>
                            </tr>
                            @endif

                            @foreach ($monthlyPayments as $i => $payment)
                            <tr class="{{ $payment->status === 'paid' ? 'payment-row-paid' : '' }}">
                                <td class="ps-3">{{ $i + 1 }}</td>
                                <td>{{ $payment->paid_date ? \Carbon\Carbon::parse($payment->paid_date)->format('d M Y') : '-' }}</td>
                                <td class="text-end">{{ number_format($payment->paid_amount) }} Ks</td>
                                <td class="text-center">
                                    @if ($payment->status === 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @elseif ($payment->paid_date && \Carbon\Carbon::parse($payment->paid_date)->isPast())
                                        <span class="badge bg-danger">Overdue</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($payment->status !== 'paid')
                                    <button class="btn btn-sm btn-success mark-paid-btn"
                                        data-id="{{ $payment->id }}"
                                        data-url="{{ route('admin.installment.markPaid', $payment->id) }}">
                                        <i class="fas fa-check"></i> Mark Paid
                                    </button>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach

                            @if ($installment->payments->isEmpty())
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No payment records found.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).on('click', '.mark-paid-btn', function () {
        const btn = $(this);
        const url = btn.data('url');

        Swal.fire({
            title: 'Mark as Paid?',
            text: 'This will record the payment as paid today.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, mark paid!',
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: url,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function (res) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: res.message,
                        timer: 2000,
                        showConfirmButton: false,
                    }).then(() => location.reload());
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Something went wrong.',
                    });
                }
            });
        });
    });
</script>
@endsection
