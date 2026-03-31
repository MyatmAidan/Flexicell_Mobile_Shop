<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Device;
use App\Models\Installment;
use App\Models\InstallmentPayment;
use App\Models\InstallmentRate;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderTradeIn;
use App\Models\Payment;
use App\Models\Phone_model;
use App\Models\Product;
use App\Models\SecondPhonePurchase;
use App\Support\VariantStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class TradeInSaleController extends Controller
{
    protected function requirePermission(string $perm): void
    {
        if (!auth()->user()?->hasPermission($perm)) {
            abort(403);
        }
    }

    public function index()
    {
        $this->requirePermission('direct_sale.manage');

        $phoneModels   = Phone_model::with('brand', 'category')->orderBy('model_name')->get();
        $ramOptions    = DB::table('ram_options')->orderBy('name')->get(['id', 'name', 'value']);
        $storageOptions = DB::table('storage_options')->orderBy('name')->get(['id', 'name', 'value']);
        $colorOptions  = DB::table('color_options')->orderBy('name')->get(['id', 'name', 'value']);
        $installmentRates = InstallmentRate::orderBy('month_option')->get(['id', 'month_option', 'rate']);

        return view('admin.trade_in.index', compact(
            'phoneModels', 'ramOptions', 'storageOptions', 'colorOptions', 'installmentRates'
        ));
    }

    public function searchCustomers(Request $request)
    {
        $this->requirePermission('direct_sale.manage');
        $q = $request->input('q', '');

        $customers = Customer::query()
            ->when($q, fn ($query) => $query
                ->where('name', 'like', '%' . $q . '%')
                ->orWhere('phone', 'like', '%' . $q . '%')
                ->orWhere('email', 'like', '%' . $q . '%'))
            ->orderByDesc('id')
            ->limit(20)
            ->get(['id', 'name', 'phone', 'email', 'address']);

        return response()->json(['data' => $customers]);
    }

    public function checkout(Request $request)
    {
        $this->requirePermission('direct_sale.manage');

        $validated = $request->validate([
            // Trade-in phone
            'tradein_phone_model_id' => 'required|exists:phone_models,id',
            'tradein_imei'           => 'required|string|max:50|unique:second_phone_purchases,imei',
            'tradein_ram'            => 'nullable|string|max:30',
            'tradein_storage'        => 'nullable|string|max:30',
            'tradein_color'          => 'nullable|string|max:50',
            'tradein_condition'      => 'required|in:A,B,C',
            'tradein_battery'        => 'required|integer|min:0|max:100',
            'tradein_buy_price'      => 'required|numeric|min:0',
            'tradein_purchase_at'    => 'nullable|date',
            'tradein_images'         => 'nullable|array',
            'tradein_images.*'       => 'nullable|string',
            // Customer: either pick existing or create new
            'customer_id'      => 'nullable|exists:customers,id',
            'customer_name'    => 'nullable|string|max:255',
            'customer_phone'   => 'nullable|string|max:50',
            'customer_nrc'     => 'nullable|string|max:100',
            'customer_address' => 'nullable|string|max:500',
            // Payment
            'payment_type'         => 'required|in:cash,installment',
            'installment_rate_id'  => 'nullable|exists:installment_rates,id',
            'down_payment'         => 'nullable|numeric|min:0',
            // Cart
            'items'                => 'required|array|min:1',
            'items.*.product_id'   => 'required|exists:products,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.unit_price'   => 'nullable|numeric|min:0',
            'items.*.discount_price' => 'nullable|numeric|min:0',
            'items.*.device_id'        => 'nullable|exists:devices,id',
            'items.*.imei'             => 'nullable|string',
            'items.*.ram_option_id'    => 'nullable|integer',
            'items.*.storage_option_id'=> 'nullable|integer',
            'items.*.color_option_id'  => 'nullable|integer',
            'items.*.product_variant_id' => 'nullable|integer|exists:product_variants,id',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $userId = Auth::id();

            // ── 1. Save trade-in phone images ────────────────────────────
            $tradeInImages = [];
            foreach (($validated['tradein_images'] ?? []) as $img) {
                if (is_string($img) && preg_match('/^data:image\/(\w+);base64,/', $img, $m)) {
                    $ext = $m[1] ?? 'jpg';
                    $data = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $img));
                    $filename = uniqid('ti_') . '.' . $ext;
                    Storage::disk('public')->put('second_purchases/' . $filename, $data);
                    $tradeInImages[] = $filename;
                } elseif (is_string($img) && !empty($img)) {
                    $tradeInImages[] = $img;
                }
            }

            // Also handle real uploaded files
            if ($request->hasFile('tradein_image_files')) {
                foreach ($request->file('tradein_image_files') as $file) {
                    $path = $file->store('second_purchases', 'public');
                    $tradeInImages[] = basename($path);
                }
            }

            // ── 2. Create SecondPhonePurchase ────────────────────────────
            $ramId     = $validated['tradein_ram']     ? VariantStock::upsertOption('ram_options',     $validated['tradein_ram'])     : null;
            $storageId = $validated['tradein_storage'] ? VariantStock::upsertOption('storage_options', $validated['tradein_storage']) : null;
            $colorId   = $validated['tradein_color']   ? VariantStock::upsertOption('color_options',   $validated['tradein_color'])   : null;

            $purchase = SecondPhonePurchase::create([
                'user_id'          => $userId,
                'phone_model_id'   => $validated['tradein_phone_model_id'],
                'imei'             => $validated['tradein_imei'],
                'ram_option_id'    => $ramId,
                'storage_option_id'=> $storageId,
                'color_option_id'  => $colorId,
                'image'            => $tradeInImages ?: null,
                'condition_grade'  => $validated['tradein_condition'],
                'battery_percentage' => $validated['tradein_battery'],
                'buy_price'        => $validated['tradein_buy_price'],
                'purchase_at'      => $validated['tradein_purchase_at'] ?? now(),
            ]);

            // ── 3. Link trade-in phone to a product / create as device ───
            // Find or create a "second hand" product for the traded-in model
            $tradeInProduct = Product::firstOrCreate(
                ['phone_model_id' => $validated['tradein_phone_model_id'], 'product_type' => 'second hand'],
                ['description' => null]
            );

            $tradeInVariantId = VariantStock::findOrCreateVariantId(
                $tradeInProduct->id,
                $ramId,
                $storageId,
                $colorId
            );

            $tradeInDevice = Device::create([
                'product_variant_id' => $tradeInVariantId,
                'second_purchase_id' => $purchase->id,
                'imei'               => $validated['tradein_imei'],
                'battery_percentage' => $validated['tradein_battery'],
                'condition_grade'    => $validated['tradein_condition'],
                'status'             => 'available',
                'purchase_price'     => $validated['tradein_buy_price'],
                'selling_price'      => 0,
                'image'              => $tradeInImages ?: null,
            ]);

            $tradeInCredit = (float) $validated['tradein_buy_price'];

            // ── 4. Process cart items ────────────────────────────────────
            $items              = $validated['items'];
            $orderItemsToCreate = [];
            $devicesToMark      = [];
            $touchedProductIds  = [];
            $totalAmount        = 0.0;
            $discountAmount     = 0.0;

            foreach ($items as $index => $line) {
                $productId    = (int) $line['product_id'];
                $quantity     = (int) ($line['quantity'] ?? 1);
                $discountPrice = (float) ($line['discount_price'] ?? 0);
                $deviceId          = $line['device_id'] ?? null;
                $imei              = $line['imei'] ?? null;
                $isDeviceLine      = !empty($deviceId) || !empty($imei);
                $ram_option_id     = $line['ram_option_id'] ?? null;
                $storage_option_id = $line['storage_option_id'] ?? null;
                $color_option_id   = $line['color_option_id'] ?? null;
                $productVariantId  = ! empty($line['product_variant_id']) ? (int) $line['product_variant_id'] : null;

                $product = Product::query()->whereKey($productId)->lockForUpdate()->firstOrFail();

                if ($isDeviceLine) {
                    $device = $deviceId
                        ? Device::query()->with('productVariant')->lockForUpdate()->whereKey($deviceId)->first()
                        : Device::query()->with('productVariant')->lockForUpdate()->where('imei', $imei)->first();

                    if (!$device) {
                        throw ValidationException::withMessages(["items.$index.device_id" => ['Device not found.']]);
                    }
                    if ($device->status !== 'available' || !is_null($device->order_id)) {
                        throw ValidationException::withMessages(["items.$index.device_id" => ['Device is not available.']]);
                    }
                    if (str_starts_with((string) $device->imei, 'PENDING-')) {
                        throw ValidationException::withMessages(["items.$index.device_id" => ['Device IMEI is not set yet.']]);
                    }

                    $unitPrice = isset($line['unit_price']) ? (float) $line['unit_price'] : (float) ($product->selling_price ?? 0);
                    $lineTotal = ($unitPrice - $discountPrice);
                    $totalAmount  += $unitPrice;
                    $discountAmount += $discountPrice;

                    $orderItemsToCreate[] = [
                        'product_variant_id' => (int) $device->product_variant_id,
                        'quantity'           => 1,
                        'device_id'            => $device->id,
                        'unit_price'           => $unitPrice,
                        'discount_price'       => $discountPrice,
                        'total'                => $lineTotal,
                        'created_at'           => now(),
                        'updated_at'           => now(),
                    ];
                    $devicesToMark[]      = $device;
                    $touchedProductIds[]  = (int) ($device->productVariant?->product_id ?? 0);
                } else {
                    if ($productVariantId) {
                        $pvRow = DB::table('product_variants')
                            ->where('id', $productVariantId)
                            ->where('product_id', $product->id)
                            ->first();
                        if (! $pvRow) {
                            throw ValidationException::withMessages(["items.$index.product_variant_id" => ['Variant does not belong to this product.']]);
                        }
                    }

                    $allocQuery = Device::query()
                        ->join('product_variants as pv', 'pv.id', '=', 'devices.product_variant_id')
                        ->where('pv.product_id', $product->id)
                        ->whereNull('devices.order_id')
                        ->where('devices.status', 'available')
                        ->select('devices.*')
                        ->lockForUpdate();

                    if ($productVariantId) {
                        $allocQuery->where('devices.product_variant_id', $productVariantId);
                    } else {
                        if ($ram_option_id) {
                            $allocQuery->where('pv.ram_option_id', $ram_option_id);
                        }
                        if ($storage_option_id) {
                            $allocQuery->where('pv.storage_option_id', $storage_option_id);
                        }
                        if ($color_option_id) {
                            $allocQuery->where('pv.color_option_id', $color_option_id);
                        }
                    }

                    $allocDevices = $allocQuery->limit($quantity)->get();
                    if ($allocDevices->count() < $quantity) {
                        throw ValidationException::withMessages(["items.$index.quantity" => ['Not enough available devices.']]);
                    }

                    $unitPrice = isset($line['unit_price']) ? (float) $line['unit_price'] : (float) ($product->selling_price ?? 0);

                    foreach ($allocDevices as $d) {
                        $lineTotal       = ($unitPrice - $discountPrice);
                        $totalAmount    += $unitPrice;
                        $discountAmount += $discountPrice;
                        $orderItemsToCreate[] = [
                            'product_variant_id' => (int) $d->product_variant_id,
                            'quantity'           => 1,
                            'device_id'            => $d->id,
                            'unit_price'           => $unitPrice,
                            'discount_price'       => $discountPrice,
                            'total'                => $lineTotal,
                            'created_at'           => now(),
                            'updated_at'           => now(),
                        ];
                        $devicesToMark[]    = $d;
                        $touchedProductIds[] = (int) $productId;
                    }
                }
            }

            // ── 5. Customer ──────────────────────────────────────────────
            // Use selected existing customer, or create a new one from form fields.
            $customer = null;
            if (!empty($validated['customer_id'])) {
                $customer = Customer::find($validated['customer_id']);
            } elseif (!empty($validated['customer_name'])) {
                $customer = Customer::create([
                    'name'    => $validated['customer_name'],
                    'phone'   => $validated['customer_phone'] ?? null,
                    'nrc'     => $validated['customer_nrc']   ?? null,
                    'address' => $validated['customer_address'] ?? null,
                ]);
            }

            // ── 6. Create Order ──────────────────────────────────────────
            $grandTotal  = max(0, $totalAmount - $discountAmount - $tradeInCredit);
            $orderStatus = $validated['payment_type'] === 'installment' ? 'installment' : 'completed';

            $order = Order::create([
                'user_id'         => $userId,
                'customer_id'     => $customer?->id,
                'total_amount'    => $totalAmount,
                'discount_amount' => $discountAmount,
                'tax_amount'      => 0,
                'shipping_amount' => 0,
                'grand_total'     => $grandTotal,
                'order_status'    => $orderStatus,
                'shipping_address'=> $validated['customer_address'] ?? null,
                'order_date'      => now(),
                'status'          => 'active',
            ]);

            foreach ($orderItemsToCreate as &$oi) {
                $oi['order_id'] = $order->id;
            }
            unset($oi);
            OrderItem::insert($orderItemsToCreate);

            foreach ($devicesToMark as $d) {
                $d->update(['order_id' => $order->id, 'status' => 'sold']);
            }

            // ── 7. Trade-in link ─────────────────────────────────────────
            OrderTradeIn::create([
                'order_id'                => $order->id,
                'second_phone_purchase_id'=> $purchase->id,
                'trade_in_credit'         => $tradeInCredit,
            ]);

            // ── 8. Payment ───────────────────────────────────────────────
            if ($validated['payment_type'] === 'cash') {
                Payment::create([
                    'order_id'     => $order->id,
                    'payment_type' => 'cash',
                    'amount'       => $grandTotal,
                    'status'       => 'completed',
                    'paid_at'      => now(),
                ]);
            } elseif ($validated['payment_type'] === 'installment') {
                $rate = InstallmentRate::findOrFail($validated['installment_rate_id']);
                $months       = (int) $rate->month_option;
                $ratePercent  = (float) $rate->rate;
                $downPayment  = (float) ($validated['down_payment'] ?? 0);
                $totalWithInt = $grandTotal * (1 + $ratePercent / 100);
                $remaining    = max(0, $totalWithInt - $downPayment);
                $monthly      = $months > 0 ? round($remaining / $months, 2) : 0;

                Payment::create([
                    'order_id'     => $order->id,
                    'payment_type' => 'installment_down',
                    'amount'       => $downPayment,
                    'status'       => 'completed',
                    'paid_at'      => now(),
                ]);

                $installment = Installment::create([
                    'order_id'            => $order->id,
                    'installment_rate_id' => $rate->id,
                    'total_amount'        => $totalWithInt,
                    'down_payment'        => $downPayment,
                    'remaining_amount'    => $remaining,
                    'monthly_amount'      => $monthly,
                    'months'              => $months,
                    'start_date'          => now()->toDateString(),
                ]);

                for ($i = 1; $i <= $months; $i++) {
                    InstallmentPayment::create([
                        'installment_id'     => $installment->id,
                        'order_id'           => $order->id,
                        'installment_number' => $i,
                        'amount'             => $monthly,
                        'due_date'           => now()->addMonths($i),
                        'status'             => 'pending',
                    ]);
                }
            }

            return response()->json([
                'status'      => true,
                'receipt_url' => route('admin.order.receipt', $order->id),
            ]);
        });
    }
}
