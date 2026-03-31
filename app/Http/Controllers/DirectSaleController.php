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
use App\Models\PaymentCustomer;
use App\Models\Product;
use App\Models\WarrantyDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class DirectSaleController extends Controller
{
    public function index()
    {
        $this->requirePermission('direct_sale.manage');
        $installmentRates = InstallmentRate::query()
            ->orderBy('month_option')
            ->get(['id', 'month_option', 'rate']);

        return view('admin.direct_sale.index', compact('installmentRates'));
    }

    public function searchProducts()
    {
        $this->requirePermission('direct_sale.manage');
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

            $fallbackDevicePrice = Device::query()
                ->whereHas('productVariant', fn ($q) => $q->where('product_id', $p->id))
                ->where('status', 'available')
                ->whereNull('order_id')
                ->whereNotNull('selling_price')
                ->orderBy('selling_price')
                ->value('selling_price');

            $effectivePrice = (float) ($p->selling_price ?? 0);
            if ($effectivePrice <= 0 && $fallbackDevicePrice !== null) {
                $effectivePrice = (float) $fallbackDevicePrice;
            }

            return [
                'id' => $p->id,
                'label' => ($p->phoneModel->model_name ?? '-') . ' (' . $p->product_type . ')',
                'brand' => $p->phoneModel?->brand?->brand_name ?? '-',
                'product_type' => $p->product_type,
                'selling_price' => $effectivePrice,
                'stock_quantity' => (int) $availableDevices,
                'available_devices' => (int) $availableDevices,
                'image' => $firstImage,
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function searchCustomers(Request $request)
    {
        $this->requirePermission('direct_sale.manage');
        $q = $request->input('q', '');

        $customers = Customer::query()
            ->when($q, function ($query) use ($q) {
                $query->where('name', 'like', '%' . $q . '%')
                    ->orWhere('phone', 'like', '%' . $q . '%')
                    ->orWhere('email', 'like', '%' . $q . '%');
            })
            ->orderByDesc('id')
            ->limit(20)
            ->get(['id', 'name', 'phone', 'email', 'address']);

        return response()->json(['data' => $customers]);
    }

    public function searchDevices()
    {
        $this->requirePermission('direct_sale.manage');
        $imei = request('imei');
        if (!$imei) {
            return response()->json(['data' => null]);
        }

        $device = Device::query()
            ->with(['productVariant.product.phoneModel.brand'])
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
        $pv = $device->productVariant;
        $effectivePrice = (float) ($device->selling_price ?? 0);
        if ($effectivePrice <= 0) {
            $effectivePrice = (float) ($product?->selling_price ?? 0);
        }

        return response()->json([
            'data' => [
                'device_id' => $device->id,
                'imei' => $device->imei,
                'product_id' => $product?->id,
                'product_variant_id' => $device->product_variant_id,
                'product_label' => ($product?->phoneModel?->model_name ?? '-') . ' (' . ($product?->product_type ?? '-') . ')',
                'brand' => $product?->phoneModel?->brand?->brand_name ?? '-',
                'selling_price' => $effectivePrice,
                'ram' => $pv?->ramOption?->value ?? '-',
                'storage' => $pv?->storageOption?->value ?? '-',
                'color' => $pv?->colorOption?->value ?? '-',
            ],
        ]);
    }

    public function checkVariantStock(Request $request)
    {
        $this->requirePermission('direct_sale.manage');
        $productId = (int) $request->product_id;
        $ram       = $request->ram;       // option ID
        $storage   = $request->storage;   // option ID
        $color     = $request->color;     // option ID

        $productVariantId = $request->input('product_variant_id');

        $query = DB::table('devices as d')
            ->join('product_variants as pv', 'pv.id', '=', 'd.product_variant_id')
            ->where('pv.product_id', $productId)
            ->whereNull('d.order_id')
            ->where('d.status', 'available')
            ->when($productVariantId, fn ($q) => $q->where('d.product_variant_id', (int) $productVariantId))
            ->when(! $productVariantId && $ram, fn ($q) => $q->where('pv.ram_option_id', $ram))
            ->when(! $productVariantId && $storage, fn ($q) => $q->where('pv.storage_option_id', $storage))
            ->when(! $productVariantId && $color, fn ($q) => $q->where('pv.color_option_id', $color));

        $count      = (clone $query)->count();
        $priceStats = (clone $query)
            ->selectRaw('MIN(d.selling_price) as min_price, MAX(d.selling_price) as max_price')
            ->first();

        return response()->json([
            'count'     => $count,
            'min_price' => $priceStats->min_price !== null ? (float) $priceStats->min_price : null,
            'max_price' => $priceStats->max_price !== null ? (float) $priceStats->max_price : null,
        ]);
    }

    public function variants(Product $product)
    {
        $this->requirePermission('direct_sale.manage');

        $optionRows = DB::table('devices as d')
            ->join('product_variants as pv', 'pv.id', '=', 'd.product_variant_id')
            ->leftJoin('ram_options as ro', 'ro.id', '=', 'pv.ram_option_id')
            ->leftJoin('storage_options as so', 'so.id', '=', 'pv.storage_option_id')
            ->leftJoin('color_options as co', 'co.id', '=', 'pv.color_option_id')
            ->where('pv.product_id', $product->id)
            ->where('d.status', 'available')
            ->whereNull('d.order_id')
            ->select([
                'ro.id as ram_id', 'ro.name as ram_name', 'ro.value as ram_value',
                'so.id as storage_id', 'so.name as storage_name', 'so.value as storage_value',
                'co.id as color_id', 'co.name as color_name', 'co.value as color_value',
            ])
            ->distinct()
            ->get();

        $ram = [];
        $storage = [];
        $color = [];

        foreach ($optionRows as $row) {
            if ($row->ram_id) {
                $ram[$row->ram_id] = ['id' => $row->ram_id, 'name' => $row->ram_name, 'value' => $row->ram_value];
            }
            if ($row->storage_id) {
                $storage[$row->storage_id] = ['id' => $row->storage_id, 'name' => $row->storage_name, 'value' => $row->storage_value];
            }
            if ($row->color_id) {
                $color[$row->color_id] = ['id' => $row->color_id, 'name' => $row->color_name, 'value' => $row->color_value];
            }
        }

        $variantRows = DB::table('product_variants as pv')
            ->where('pv.product_id', $product->id)
            ->where('pv.is_active', true)
            ->leftJoin('ram_options as ro', 'ro.id', '=', 'pv.ram_option_id')
            ->leftJoin('storage_options as so', 'so.id', '=', 'pv.storage_option_id')
            ->leftJoin('color_options as co', 'co.id', '=', 'pv.color_option_id')
            ->select([
                'pv.id as product_variant_id',
                'pv.ram_option_id', 'pv.storage_option_id', 'pv.color_option_id',
                'ro.name as ram_name', 'so.name as storage_name', 'co.name as color_name',
            ])
            ->orderBy('ro.name')
            ->orderBy('so.name')
            ->orderBy('co.name')
            ->get();

        $combinations = [];
        foreach ($variantRows as $vr) {
            $vid = (int) $vr->product_variant_id;
            $stock = (int) DB::table('devices')
                ->where('product_variant_id', $vid)
                ->whereNull('order_id')
                ->where('status', 'available')
                ->count();

            if ($stock < 1) {
                continue;
            }

            $priceRow = DB::table('devices')
                ->where('product_variant_id', $vid)
                ->whereNull('order_id')
                ->where('status', 'available')
                ->selectRaw('MIN(selling_price) as min_p, MAX(selling_price) as max_p')
                ->first();

            $labelParts = array_values(array_filter([
                $vr->ram_name,
                $vr->storage_name,
                $vr->color_name,
            ], fn ($x) => $x !== null && $x !== ''));

            if ($labelParts === []) {
                $labelParts = ['Placeholder / IMEI pending'];
            }

            $combinations[] = [
                'product_variant_id' => $vid,
                'ram_option_id'      => $vr->ram_option_id !== null ? (int) $vr->ram_option_id : null,
                'storage_option_id'  => $vr->storage_option_id !== null ? (int) $vr->storage_option_id : null,
                'color_option_id'    => $vr->color_option_id !== null ? (int) $vr->color_option_id : null,
                'label'              => implode(' · ', $labelParts),
                'stock'              => $stock,
                'min_price'          => $priceRow && $priceRow->min_p !== null ? (float) $priceRow->min_p : null,
                'max_price'          => $priceRow && $priceRow->max_p !== null ? (float) $priceRow->max_p : null,
            ];
        }

        return response()->json([
            'data' => [
                'product_id'   => $product->id,
                'ram'          => array_values($ram),
                'storage'      => array_values($storage),
                'color'        => array_values($color),
                'combinations' => $combinations,
            ],
        ]);
    }

    public function receipt(Order $order)
    {
        $this->requirePermission('direct_sale.manage');
        $order->load([
            'items.product.phoneModel.brand',
            'items.device',
            'installment.rate',
            'installment.payments',
        ]);

        return view('admin.order.receipt', compact('order'));
    }

    public function checkout(DirectSaleCheckoutRequest $request)
    {
        $this->requirePermission('direct_sale.manage');
        $payload = $request->validated();
        $userId = Auth::id();

        $result = DB::transaction(function () use ($payload, $userId, $request) {
            $items = $payload['items'];
            $paymentType = $payload['payment_type'];
            $customerId = (int) $payload['customer_id'];

            $customer = Customer::findOrFail($customerId);

            $orderItemsToCreate = [];
            $devicesToUpdate = [];
            $touchedProductIds = [];

            $totalAmount = 0.0;
            $discountAmount = 0.0;

            foreach ($items as $index => $line) {
                $productId = (int) ($line['product_id'] ?? null);
                $quantity = (int) ($line['quantity'] ?? 1);
                $ram_option_id = $line['ram_option_id'] ?? null;
                $storage_option_id = $line['storage_option_id'] ?? null;
                $color_option_id = $line['color_option_id'] ?? null;
                $productVariantId = isset($line['product_variant_id']) ? (int) $line['product_variant_id'] : null;

                $discountPrice = (float) ($line['discount_price'] ?? 0);

                $deviceId = $line['device_id'] ?? null;
                $imei = $line['imei'] ?? null;
                $isDeviceLine = !empty($deviceId) || !empty($imei);

                if ($isDeviceLine) {
                    $productForDevice = Product::query()->whereKey($productId)->lockForUpdate()->first();
                    if (!$productForDevice) {
                        throw ValidationException::withMessages(["items.$index.product_id" => ['Product not found.']]);
                    }

                    $deviceQuery = Device::query()->with('productVariant')->lockForUpdate();
                    $device = $deviceId
                        ? $deviceQuery->whereKey($deviceId)->first()
                        : $deviceQuery->where('imei', $imei)->first();

                    if (!$device) {
                        throw ValidationException::withMessages(["items.$index.device_id" => ['Device not found.']]);
                    }

                    if ((int) ($device->productVariant?->product_id) !== $productId) {
                        throw ValidationException::withMessages(["items.$index.product_id" => ['Device does not belong to the selected product.']]);
                    }

                    if ($device->status !== 'available' || !is_null($device->order_id)) {
                        throw ValidationException::withMessages(["items.$index.device_id" => ['Device is not available for sale.']]);
                    }

                    if (is_string($device->imei) && str_starts_with($device->imei, 'PENDING-')) {
                        throw ValidationException::withMessages(["items.$index.device_id" => ['Device IMEI is not set yet.']]);
                    }

                    $unitPrice = isset($line['unit_price'])
                        ? (float) $line['unit_price']
                        : (float) ($productForDevice->selling_price ?? 0);

                    if ($discountPrice > $unitPrice) {
                        throw ValidationException::withMessages(["items.$index.discount_price" => ['Discount cannot exceed unit price.']]);
                    }

                    $lineTotal = ($unitPrice - $discountPrice) * 1;
                    $totalAmount += $unitPrice;
                    $discountAmount += $discountPrice;

                    $orderItemsToCreate[] = [
                        'product_variant_id' => (int) $device->product_variant_id,
                        'quantity' => 1,
                        'device_id' => $device->id,
                        'unit_price' => $unitPrice,
                        'discount_price' => $discountPrice,
                        'total' => $lineTotal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $devicesToUpdate[] = $device;
                    $touchedProductIds[] = (int) ($device->productVariant?->product_id ?? 0);
                } else {
                    $product = Product::query()->whereKey($productId)->lockForUpdate()->first();
                    if (!$product) {
                        throw ValidationException::withMessages(["items.$index.product_id" => ['Product not found.']]);
                    }

                    if ($product->product_type !== 'new') {
                        throw ValidationException::withMessages(["items.$index.product_id" => ['Second-hand products must be sold by selecting a device (IMEI).']]);
                    }

                    if ($quantity < 1) {
                        throw ValidationException::withMessages(["items.$index.quantity" => ['Quantity must be at least 1.']]);
                    }

                    if ($productVariantId) {
                        $pvRow = DB::table('product_variants')
                            ->where('id', $productVariantId)
                            ->where('product_id', $product->id)
                            ->first();
                        if (! $pvRow) {
                            throw ValidationException::withMessages(["items.$index.product_variant_id" => ['Variant does not belong to this product.']]);
                        }
                    }

                    $deviceAllocQuery = Device::query()
                        ->select('devices.*')
                        ->join('product_variants as pv', 'pv.id', '=', 'devices.product_variant_id')
                        ->where('pv.product_id', $product->id)
                        ->whereNull('devices.order_id')
                        ->where('devices.status', 'available')
                        ->lockForUpdate();

                    if ($productVariantId) {
                        $deviceAllocQuery->where('devices.product_variant_id', $productVariantId);
                    } else {
                        if ($ram_option_id) {
                            $deviceAllocQuery->where('pv.ram_option_id', $ram_option_id);
                        }
                        if ($storage_option_id) {
                            $deviceAllocQuery->where('pv.storage_option_id', $storage_option_id);
                        }
                        if ($color_option_id) {
                            $deviceAllocQuery->where('pv.color_option_id', $color_option_id);
                        }
                    }

                    $allocDevices = $deviceAllocQuery->limit($quantity)->get();
                    if ($allocDevices->count() < $quantity) {
                        throw ValidationException::withMessages(["items.$index.quantity" => ['Insufficient available devices for the selected variant.']]);
                    }

                    $unitPrice = isset($line['unit_price'])
                        ? (float) $line['unit_price']
                        : (float) ($product->selling_price ?? 0);

                    if ($discountPrice > $unitPrice) {
                        throw ValidationException::withMessages(["items.$index.discount_price" => ['Discount cannot exceed unit price.']]);
                    }

                    foreach ($allocDevices as $d) {
                        $lineTotal = ($unitPrice - $discountPrice) * 1;
                        $totalAmount += $unitPrice;
                        $discountAmount += $discountPrice;

                        $orderItemsToCreate[] = [
                            'product_variant_id' => (int) $d->product_variant_id,
                            'quantity' => 1,
                            'device_id' => $d->id,
                            'unit_price' => $unitPrice,
                            'discount_price' => $discountPrice,
                            'total' => $lineTotal,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                        $devicesToUpdate[] = $d;
                        $touchedProductIds[] = (int) $productId;
                    }
                }
            }

            $grandTotal = max(0, $totalAmount - $discountAmount);

            $orderStatus = $paymentType === 'installment' ? 'installment' : 'completed';

            $order = Order::create([
                'user_id' => $userId,
                'customer_id' => $customer->id,
                'total_amount' => $totalAmount,
                'discount_amount' => $discountAmount,
                'tax_amount' => 0,
                'shipping_amount' => 0,
                'grand_total' => $grandTotal,
                'order_status' => $orderStatus,
                'shipping_address' => $customer->address,
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

                if ($device->warranty_id) {
                    $warranty = $device->warranty;
                    if ($warranty) {
                        $startDate = now()->toDateString();
                        $endDate = now()->addMonths($warranty->warranty_month)->toDateString();
                        WarrantyDetail::create([
                            'warranty_id' => $warranty->id,
                            'device_id'   => $device->id,
                            'customer_id' => $customer->id,
                            'start_date'  => $startDate,
                            'end_date'    => $endDate,
                            'status'      => 'active',
                        ]);
                    }
                }
            }

            if ($paymentType === 'cash') {
                \App\Models\Payment::create([
                    'order_id' => $order->id,
                    'payment_type' => 'cash',
                    'amount' => $grandTotal,
                    'status' => 'completed',
                    'paid_at' => now(),
                ]);
            }

            if ($paymentType === 'installment') {
                $installmentRateId = $payload['installment_rate_id'];
                $rate = InstallmentRate::query()->whereKey($installmentRateId)->firstOrFail();

                $months = (int) $rate->month_option;
                $ratePercent = (float) $rate->rate;
                $downPayment = (float) ($payload['down_payment'] ?? 0);

                $totalWithInterest = $grandTotal * (1 + ($ratePercent / 100));
                if ($downPayment < 0 || $downPayment > $totalWithInterest) {
                    throw ValidationException::withMessages(['down_payment' => ['Down payment must be between 0 and total amount.']]);
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

                    \App\Models\Payment::create([
                        'order_id' => $order->id,
                        'payment_type' => 'installment',
                        'amount' => $downPayment,
                        'status' => 'completed',
                        'paid_at' => now(),
                    ]);
                }

                for ($i = 1; $i <= $months; $i++) {
                    InstallmentPayment::create([
                        'installment_id' => $installment->id,
                        'paid_amount'    => round($monthly, 2),
                        'status'         => 'pending',
                        'paid_date'      => now()->addMonths($i)->toDateString(),
                    ]);
                }

                $attachments = [];
                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        $attachments[] = $file->store('customer_attachments', 'public');
                    }
                }

                PaymentCustomer::create([
                    'order_id'       => $order->id,
                    'customer_id'    => $customer->id,
                    'payment_method' => 'installment',
                    'amount'         => $grandTotal,
                    'nrc'            => $payload['customer_nrc'] ?? null,
                    'attachments'    => $attachments ?: null,
                ]);
            }

            return [
                'order_id' => $order->id,
                'grand_total' => $order->grand_total,
                'receipt_url' => route('admin.order.receipt', $order->id),
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Checkout completed successfully.',
            'data' => $result,
        ]);
    }
}
