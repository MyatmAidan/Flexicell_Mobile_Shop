<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $this->requirePermission('orders.view');
        return view('admin.order.index');
    }

    public function getList(Request $request)
    {
        $this->requirePermission('orders.view');
        $query = Order::with(['items', 'installment', 'customer'])
            ->orderByDesc('id');

        // DataTables manual handling
        $totalRecords = $query->count();

        // Search
        $searchValue = $request->input('search.value');
        if ($searchValue) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('id', 'like', "%{$searchValue}%")
                    ->orWhere('shipping_address', 'like', "%{$searchValue}%")
                    ->orWhere('order_status', 'like', "%{$searchValue}%")
                    ->orWhereHas('customer', function ($q2) use ($searchValue) {
                        $q2->where('name', 'like', "%{$searchValue}%");
                    });
            });
        }

        $filteredRecords = $query->count();

        // Order
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        $columns = [
            'id', // Index 0 (DT_RowIndex is mapped separately, but index 0 in request comes from the first column)
            'id', // Index 1
            'shipping_address', // Index 2 (mapped to customer name display but column is still shipping_address in DB)
            null, // Index 3
            'grand_total', // Index 4
            'order_status', // Index 5
            'order_date', // Index 6
            null // Index 7
        ];
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        if ($orderColumn) {
            $query->orderBy($orderColumn, $orderDir);
        }

        // Paginate
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $orders = $query->skip($start)->take($length)->get();

        $data = $orders->map(function (Order $order, $index) use ($start) {
            $statusColors = [
                'completed'   => 'success',
                'installment' => 'warning',
                'pending'     => 'secondary',
                'cancelled'   => 'danger',
            ];
            $statusColor = $statusColors[$order->order_status] ?? 'secondary';
            $statusBadge = '<span class="badge bg-' . $statusColor . '">' . ucfirst($order->order_status) . '</span>';

            $customer = $order->customer
                ? htmlspecialchars($order->customer->name, ENT_QUOTES)
                : '<span class="text-muted">Walk-in</span>';

            $itemsCount = $order->items->count();

            $showUrl = route('admin.order.show', $order->id);

            return [
                'DT_RowIndex'   => $start + $index + 1,
                'id'            => '#' . str_pad($order->id, 5, '0', STR_PAD_LEFT),
                'customer'      => $customer,
                'items_count'   => $itemsCount . ' item' . ($itemsCount !== 1 ? 's' : ''),
                'grand_total'   => number_format($order->grand_total) . ' Ks',
                'order_status'  => $statusBadge,
                'order_date'    => $order->order_date ? $order->order_date->format('d M Y') : '-',
                'action'        => '<a href="' . $showUrl . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> View</a>',
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
        $this->requirePermission('orders.view');
        $order = Order::with([
            'items.product.phoneModel.brand',
            'items.device',
            'installment.rate',
            'installment.payments',
            'tradeIn.secondPhonePurchase.phoneModel.brand',
            'tradeIn.secondPhonePurchase.ramOption',
            'tradeIn.secondPhonePurchase.storageOption',
            'tradeIn.secondPhonePurchase.colorOption',
        ])->findOrFail($id);

        return view('admin.order.show', compact('order'));
    }

    public function receipt($id)
    {
        $this->requirePermission('orders.view');
        $order = Order::with([
            'items.product.phoneModel.brand',
            'items.device',
            'installment.rate',
            'installment.payments',
            'tradeIn.secondPhonePurchase.phoneModel.brand',
            'tradeIn.secondPhonePurchase.ramOption',
            'tradeIn.secondPhonePurchase.storageOption',
            'tradeIn.secondPhonePurchase.colorOption',
        ])->findOrFail($id);

        return view('admin.order.receipt', compact('order'));
    }
}
