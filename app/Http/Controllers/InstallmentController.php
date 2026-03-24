<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\InstallmentPayment;
use Illuminate\Http\Request;

class InstallmentController extends Controller
{
    public function index()
    {
        return view('admin.installment.index');
    }

    public function getList(Request $request)
    {
        $query = Installment::with(['order.customer', 'rate', 'payments'])
            ->orderByDesc('id');

        $totalRecords = $query->count();

        // Search
        $searchValue = $request->input('search.value');
        if ($searchValue) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('id', 'like', "%{$searchValue}%")
                  ->orWhere('order_id', 'like', "%{$searchValue}%");
            })->orWhereHas('order', function ($q) use ($searchValue) {
                $q->where('shipping_address', 'like', "%{$searchValue}%")
                  ->orWhereHas('customer', function($sq) use ($searchValue) {
                      $sq->where('name', 'like', "%{$searchValue}%");
                  });
            });
        }

        $filteredRecords = $query->count();

        // Order
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        $columns = ['id', 'order_id', null, 'total_amount', 'down_payment', 'monthly_amount', 'months', 'start_date', null, null];
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        if ($orderColumn) {
            $query->orderBy($orderColumn, $orderDir);
        }

        // Paginate
        $start  = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $installments = $query->skip($start)->take($length)->get();

        $data = $installments->map(function (Installment $inst, $index) use ($start) {
            $paidCount    = $inst->payments->where('status', 'paid')->count();
            $totalPayments = $inst->payments->count();

            if ($paidCount >= $totalPayments && $totalPayments > 0) {
                $progressColor = 'success';
            } elseif ($paidCount > 0) {
                $progressColor = 'warning';
            } else {
                $progressColor = 'secondary';
            }

            $progressBadge = '<span class="badge bg-' . $progressColor . '">'
                . $paidCount . '/' . $totalPayments . ' paid</span>';

            $customer = $inst->order?->customer?->name
                ? htmlspecialchars($inst->order->customer->name, ENT_QUOTES)
                : ($inst->order?->shipping_address ? htmlspecialchars($inst->order->shipping_address, ENT_QUOTES) : '<span class="text-muted">Walk-in</span>');

            $showUrl  = route('admin.installment.show', $inst->id);
            $orderUrl = route('admin.order.show', $inst->order_id);

            return [
                'DT_RowIndex'    => $start + $index + 1,
                'id'             => '#' . str_pad($inst->id, 5, '0', STR_PAD_LEFT),
                'order_id'       => '<a href="' . $orderUrl . '" class="text-primary fw-semibold">#'
                    . str_pad($inst->order_id, 5, '0', STR_PAD_LEFT) . '</a>',
                'customer'       => $customer,
                'total_amount'   => number_format($inst->total_amount) . ' Ks',
                'down_payment'   => number_format($inst->down_payment) . ' Ks',
                'monthly_amount' => number_format($inst->monthly_amount) . ' Ks',
                'months'         => $inst->months . ' mo.',
                'start_date'     => $inst->start_date ? $inst->start_date->format('d M Y') : '-',
                'progress'       => $progressBadge,
                'action'         => '<a href="' . $showUrl . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> View</a>',
            ];
        });

        return response()->json([
            'draw'            => (int) $request->input('draw'),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data,
        ]);
    }

    public function show($id)
    {
        $installment = Installment::with([
            'order.items.product.phoneModel.brand',
            'order.items.device',
            'rate',
            'payments',
        ])->findOrFail($id);

        return view('admin.installment.show', compact('installment'));
    }

    public function markPaid($paymentId)
    {
        $payment = InstallmentPayment::with('installment')->findOrFail($paymentId);

        if ($payment->status === 'paid') {
            return response()->json(['status' => false, 'message' => 'Already marked as paid.'], 422);
        }

        $payment->update([
            'status'    => 'paid',
            'paid_date' => now()->toDateString(),
        ]);

        return response()->json(['status' => true, 'message' => 'Payment marked as paid.']);
    }
}
