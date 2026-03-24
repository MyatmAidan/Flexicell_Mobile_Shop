<?php

namespace App\Http\Controllers;

use App\Http\Requests\DirectSaleCheckoutRequest;
use App\Models\Customer;
use App\Models\Device;
use App\Models\Installment;
use App\Models\InstallmentPayment;
use App\Models\InstallmentRate;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DirectSaleController extends Controller
{
    public function index()
    {
        $installmentRates = InstallmentRate::query()
            ->orderBy('month_option')
            ->get(['id', 'month_option', 'rate']);

        return view('admin.direct_sale.index', compact('installmentRates'));
    }

    public function searchProducts()
    {
        $q = request('q');

        $products = Product::query()
            ->with(['phoneModel.brand'])
            ->when($q, function ($query) use ($q) {
                $query->whereHas('phoneModel', function ($q2) use ($q) {
                    $q2->where('model_name', 'like', '%' . $q . '%');
                })->orWhere('product_type', 'like', '%' . $q . '%');
            })
            ->orderByDesc('id')
            ->limit(30)
            ->get();

        $data = $products->map(function (Product $p) {
            $firstImage = is_array($p->image) ? ($p->image[0] ?? null) : $p->image;
            $availableDevices = $p->devices()
                ->where('status', 'available')
                ->whereNull('order_id')
                ->count();

            return [
                'id' => $p->id,
                'label' => ($p->phoneModel->model_name ?? '-') . ' (' . $p->product_type . ')',
                'brand' => $p->phoneModel?->brand?->brand_name ?? '-',
                'product_type' => $p->product_type,
                'selling_price' => (float) $p->selling_price,
                'stock_quantity' => (int) $p->stock_quantity,
                'available_devices' => (int) $availableDevices,
                'image' => $firstImage,
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function searchDevices()
    {
        $imei = request('imei');
        if (!$imei) {
            return response()->json(['data' => null]);
        }

        $device = Device::query()
            ->with(['product.phoneModel.brand'])
            ->where('imei', $imei)
            ->first();

        if (!$device) {
            return response()->json(['data' => null]);
        }

        if ($device->status !== 'available' || !is_null($device->order_id)) {
            return response()->json([
                'data' => null,
                'message' => 'Device is not available for sale.',
            ], 422);
        }

        $product = $device->product;

        return response()->json([
            'data' => [
                'device_id' => $device->id,
                'imei' => $device->imei,
                'product_id' => $device->product_id,
                'product_label' => ($product?->phoneModel?->model_name ?? '-') . ' (' . ($product?->product_type ?? '-') . ')',
                'brand' => $product?->phoneModel?->brand?->brand_name ?? '-',
                'selling_price' => (float) ($product?->selling_price ?? 0),
                'ram' => $device->ram,
                'storage' => $device->storage,
                'color' => $device->color,
            ],
        ]);
    }

    public function checkout(DirectSaleCheckoutRequest $request)
    {
        $payload = $request->validated();
        $userId = Auth::id();

        $result = DB::transaction(function () use ($payload, $userId, $request) {
            $items = $payload['items'];
            $paymentType = $payload['payment_type'];

            $orderItemsToCreate = [];
            $devicesToUpdate = [];

            $totalAmount = 0.0;
            $discountAmount = 0.0;

            foreach ($items as $index => $line) {
                $productId = (int) $line['product_id'];
                $quantity = (int) ($line['quantity'] ?? 1);
                $discountPrice = (float) ($line['discount_price'] ?? 0);

                $deviceId = $line['device_id'] ?? null;
                $imei = $line['imei'] ?? null;
                $isDeviceLine = !empty($deviceId) || !empty($imei);

                if ($isDeviceLine) {
                    $productForDevice = Product::query()->whereKey($productId)->lockForUpdate()->first();
                    if (!$productForDevice) {
                        throw ValidationException::withMessages([
                            "items.$index.product_id" => ['Product not found.'],
                        ]);
                    }

                    $deviceQuery = Device::query()->lockForUpdate();
                    $device = $deviceId
                        ? $deviceQuery->whereKey($deviceId)->first()
                        : $deviceQuery->where('imei', $imei)->first();

                    if (!$device) {
                        throw ValidationException::withMessages([
                            "items.$index.device_id" => ['Device not found.'],
                        ]);
                    }

                    if ($device->product_id !== $productId) {
                        throw ValidationException::withMessages([
                            "items.$index.product_id" => ['Device does not belong to the selected product.'],
                        ]);
                    }

                    if ($device->status !== 'available' || !is_null($device->order_id)) {
                        throw ValidationException::withMessages([
                            "items.$index.device_id" => ['Device is not available for sale.'],
                        ]);
                    }

                    $unitPrice = isset($line['unit_price'])
                        ? (float) $line['unit_price']
                        : (float) ($productForDevice->selling_price ?? 0);

                    if ($discountPrice > $unitPrice) {
                        throw ValidationException::withMessages([
                            "items.$index.discount_price" => ['Discount cannot exceed unit price.'],
                        ]);
                    }

                    $lineTotal = ($unitPrice - $discountPrice) * 1;
                    $totalAmount += ($unitPrice * 1);
                    $discountAmount += ($discountPrice * 1);

                    $orderItemsToCreate[] = [
                        'product_id' => $productId,
                        'quantity' => 1,
                        'device_id' => $device->id,
                        'unit_price' => $unitPrice,
                        'discount_price' => $discountPrice,
                        'total' => $lineTotal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $devicesToUpdate[] = $device;
                } else {
                    $product = Product::query()->whereKey($productId)->lockForUpdate()->first();
                    if (!$product) {
                        throw ValidationException::withMessages([
                            "items.$index.product_id" => ['Product not found.'],
                        ]);
                    }

                    if ($product->product_type !== 'new') {
                        throw ValidationException::withMessages([
                            "items.$index.product_id" => ['Second-hand products must be sold by selecting a device (IMEI).'],
                        ]);
                    }

                    if ($quantity < 1) {
                        throw ValidationException::withMessages([
                            "items.$index.quantity" => ['Quantity must be at least 1.'],
                        ]);
                    }

                    if ((int) $product->stock_quantity < $quantity) {
                        throw ValidationException::withMessages([
                            "items.$index.quantity" => ["Insufficient stock. Available: {$product->stock_quantity}."],
                        ]);
                    }

                    $unitPrice = isset($line['unit_price'])
                        ? (float) $line['unit_price']
                        : (float) ($product->selling_price ?? 0);

                    if ($discountPrice > $unitPrice) {
                        throw ValidationException::withMessages([
                            "items.$index.discount_price" => ['Discount cannot exceed unit price.'],
                        ]);
                    }

                    $lineTotal = ($unitPrice - $discountPrice) * $quantity;
                    $totalAmount += ($unitPrice * $quantity);
                    $discountAmount += ($discountPrice * $quantity);

                    $orderItemsToCreate[] = [
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'device_id' => null,
                        'unit_price' => $unitPrice,
                        'discount_price' => $discountPrice,
                        'total' => $lineTotal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $product->decrement('stock_quantity', $quantity);
                }
            }

            $grandTotal = max(0, $totalAmount - $discountAmount);

            $customer = null;
            if (!empty($payload['customer_name'])) {
                $attachments = [];
                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        $path = $file->store('customer_attachments', 'public');
                        $attachments[] = $path;
                    }
                }

                $customer = Customer::create([
                    'name' => $payload['customer_name'],
                    'phone' => $payload['customer_phone'] ?? null,
                    'nrc' => $payload['customer_nrc'] ?? null,
                    'address' => $payload['customer_address'] ?? null,
                    'attachments' => $attachments,
                ]);
            }

            $orderStatus = $paymentType === 'installment' ? 'installment' : 'completed';

            $order = Order::create([
                'user_id' => $userId,
                'customer_id' => $customer?->id,
                'total_amount' => $totalAmount,
                'discount_amount' => $discountAmount,
                'tax_amount' => 0,
                'shipping_amount' => 0,
                'grand_total' => $grandTotal,
                'order_status' => $orderStatus,
                'shipping_address' => $payload['customer_address'] ?? null,
                'order_date' => now(),
                'delivered_at' => null,
                'status' => 'active',
            ]);

            foreach ($orderItemsToCreate as &$oi) {
                $oi['order_id'] = $order->id;
            }
            unset($oi);

            OrderItem::insert($orderItemsToCreate);

            foreach ($devicesToUpdate as $device) {
                $device->update([
                    'order_id' => $order->id,
                    'status' => 'sold',
                ]);
            }

            if ($paymentType === 'installment') {
                $installmentRateId = $payload['installment_rate_id'] ?? null;
                if (!$installmentRateId) {
                    throw ValidationException::withMessages([
                        'installment_rate_id' => ['Installment plan is required for installment payment.'],
                    ]);
                }

                $rate = InstallmentRate::query()->whereKey($installmentRateId)->first();
                if (!$rate) {
                    throw ValidationException::withMessages([
                        'installment_rate_id' => ['Invalid installment plan.'],
                    ]);
                }

                $months = (int) $rate->month_option;
                $ratePercent = (float) $rate->rate;
                $downPayment = (float) ($payload['down_payment'] ?? 0);

                $totalWithInterest = $grandTotal * (1 + ($ratePercent / 100));
                if ($downPayment < 0 || $downPayment > $totalWithInterest) {
                    throw ValidationException::withMessages([
                        'down_payment' => ['Down payment must be between 0 and total amount.'],
                    ]);
                }

                $remaining = $totalWithInterest - $downPayment;
                $monthly = $months > 0 ? ($remaining / $months) : $remaining;

                $installment = Installment::create([
                    'order_id' => $order->id,
                    'installment_rate_id' => $rate->id,
                    'total_amount' => $totalWithInterest,
                    'down_payment' => $downPayment,
                    'remaining_amount' => $remaining,
                    'months' => $months,
                    'monthly_amount' => $monthly,
                    'start_date' => now()->toDateString(),
                ]);

                if ($downPayment > 0) {
                    InstallmentPayment::create([
                        'installment_id' => $installment->id,
                        'paid_amount'    => $downPayment,
                        'status'         => 'paid',
                        'paid_date'      => now()->toDateString(),
                    ]);
                }

                // Generate monthly payment schedule rows
                for ($i = 1; $i <= $months; $i++) {
                    InstallmentPayment::create([
                        'installment_id' => $installment->id,
                        'paid_amount'    => round($monthly, 2),
                        'status'         => 'pending',
                        'paid_date'      => now()->addMonths($i)->toDateString(),
                    ]);
                }
            }

            return [
                'order_id' => $order->id,
                'grand_total' => $order->grand_total,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Checkout completed successfully.',
            'data' => $result,
        ]);
    }
}

