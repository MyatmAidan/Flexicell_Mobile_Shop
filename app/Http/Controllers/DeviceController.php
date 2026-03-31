<?php

namespace App\Http\Controllers;

use App\Support\VariantStock;
use App\Models\Device;
use App\Models\Product;
use App\Models\Warranty;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\DeviceCreateRequest;
use App\Http\Requests\DeviceUpdateRequest;
use Yajra\DataTables\Facades\DataTables;

class DeviceController extends Controller
{
    public function index()
    {
        $this->requirePermission('devices.view');
        $ramOptions = DB::table('ram_options')->orderBy('name')->get(['id', 'name', 'value']);
        $storageOptions = DB::table('storage_options')->orderBy('name')->get(['id', 'name', 'value']);
        $colorOptions = DB::table('color_options')->orderBy('name')->get(['id', 'name', 'value']);
        $warranties = Warranty::orderBy('warranty_month')->get();
        return view('admin.device.index', compact('ramOptions', 'storageOptions', 'colorOptions', 'warranties'));
    }

    public function getList()
    {
        $this->requirePermission('devices.view');
        $devices = Device::with('productVariant.product.phoneModel.brand');

        return DataTables::of($devices)
            ->addColumn('plus-icon', function () {
                return null;
            })
            ->addIndexColumn()
            ->addColumn('product_name', function ($device) {
                $product = $device->product;
                if (! $product || ! $product->phoneModel) {
                    return '-';
                }
                return $product->phoneModel->model_name . ' (' . $product->product_type . ')';
            })
            ->addColumn('brand_name', function ($device) {
                return $device->product?->phoneModel?->brand?->brand_name ?? '-';
            })
            ->addColumn('ram', function ($device) {
                return $device->productVariant?->ramOption?->value ?? '-';
            })
            ->addColumn('storage', function ($device) {
                return $device->productVariant?->storageOption?->value ?? '-';
            })
            ->editColumn('purchase_price', function ($device) {
                return $device->purchase_price ? number_format($device->purchase_price, 2) : '-';
            })
            ->editColumn('selling_price', function ($device) {
                return $device->selling_price ? number_format($device->selling_price, 2) : '-';
            })
            ->editColumn('color', function ($device) {
                 $color = $device->productVariant?->colorOption?->value ?? '#000000';
                 $name = $device->productVariant?->colorOption?->name ?? $color;
                 if (preg_match('/^#[a-f0-9]{6}$/i', $color) || preg_match('/^#[a-f0-9]{3}$/i', $color)) {
                      return '<div class="mx-auto" style="display:inline-block; width: 20px; height: 20px; background-color: ' . htmlspecialchars($color, ENT_QUOTES) . '; border-radius: 50%; border: 1px solid #ddd;" title="' . htmlspecialchars($name, ENT_QUOTES) . '"></div>';
                 }
                 return $name;
            })
            ->addColumn('action', function ($device) {
                return '<div class="action-btn" role="group">' .
                    '<button type="button" class="btn btn-sm mx-2 px-3 py-2 btn-primary edit-device-btn" data-id="' . $device->id . '" title="edit"><i class="fas fa-edit"></i></button>' .
                    '<a href="#" class="btn btn-danger btn-sm px-3 py-2 delete-btn" data-id="' . $device->id . '" title="delete"><i class="fa fa-trash-alt"></i></a>' .
                    '</div>';
            })
            ->rawColumns(['action', 'plus-icon', 'color'])
            ->make(true);
    }

    public function getData($id)
    {
        $this->requirePermission('devices.view');
        $device = Device::with('productVariant.product.phoneModel.brand')->findOrFail($id);
        $products = Product::with('phoneModel.brand')->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'label' => ($p->phoneModel->model_name ?? '-') . ' (' . $p->product_type . ') - ' . ($p->phoneModel->brand->brand_name ?? ''),
            ];
        });

        return response()->json([
            'device' => [
                'id' => $device->id,
                'product_id' => $device->productVariant?->product_id,
                'product_variant_id' => $device->product_variant_id,
                'imei' => $device->imei,
                'ram_option_id' => $device->productVariant?->ram_option_id,
                'storage_option_id' => $device->productVariant?->storage_option_id,
                'color_option_id' => $device->productVariant?->color_option_id,
                'warranty_id' => $device->warranty_id,
                'battery_percentage' => $device->battery_percentage,
                'condition_grade' => $device->condition_grade,
                'status' => $device->status,
                'purchase_price' => $device->purchase_price,
                'selling_price' => $device->selling_price,
                'image' => is_array($device->image) ? $device->image : ($device->image ? (array) $device->image : []),
            ],
            'products' => $products,
        ]);
    }

    public function create()
    {
        $this->requirePermission('devices.create');
        $products = Product::with('phoneModel.brand')->get();
        $ramOptions = DB::table('ram_options')->orderBy('value')->get();
        $storageOptions = DB::table('storage_options')->orderBy('value')->get();
        $colorOptions = DB::table('color_options')->orderBy('value')->get();
        $warranties = Warranty::orderBy('warranty_month')->get();
        return view('admin.device.create', compact('products', 'ramOptions', 'storageOptions', 'colorOptions', 'warranties'));
    }

    public function store(DeviceCreateRequest $request)
    {
        $this->requirePermission('devices.create');
        try {
            $imageFiles = [];

            if ($request->has('image') && is_array($request->image)) {
                foreach ($request->image as $img) {
                    if (is_string($img) && preg_match('/^data:image\/(\w+);base64,/', $img, $matches)) {
                        $extension = $matches[1] ?? 'png';
                        $data = preg_replace('/^data:image\/(\w+);base64,/', '', $img);
                        $data = str_replace(' ', '+', $data);
                        $decoded = base64_decode($data);

                        $filename = uniqid('device_') . '.' . $extension;
                        Storage::disk('public')->put('devices/' . $filename, $decoded);
                        $imageFiles[] = $filename;
                    } elseif (is_string($img) && !empty($img)) {
                        $imageFiles[] = $img;
                    }
                }
            }

            $colorOptionId = $request->color_option_id;
            if ($request->filled('new_color_name')) {
                $colorOptionId = VariantStock::getOrCreateColorOption($request->new_color_name, $request->new_color_value);
            }

            $variantId = VariantStock::findOrCreateVariantId(
                (int) $request->product_id,
                $request->ram_option_id ? (int) $request->ram_option_id : null,
                $request->storage_option_id ? (int) $request->storage_option_id : null,
                $colorOptionId
            );

            $device = Device::create([
                'product_variant_id' => $variantId,
                'imei' => $request->imei,
                'warranty_id' => $request->warranty_id ?: null,
                'battery_percentage' => $request->battery_percentage,
                'condition_grade' => $request->condition_grade,
                'status' => $request->status ?? 'available',
                'purchase_price' => $request->purchase_price,
                'selling_price' => $request->selling_price,
                'image' => $imageFiles ?: null,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Device created successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create device: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $this->requirePermission('devices.update');
        $device = Device::with('productVariant.product.phoneModel')->findOrFail($id);
        $products = Product::with('phoneModel.brand')->get();
        $ramOptions = DB::table('ram_options')->orderBy('value')->get();
        $storageOptions = DB::table('storage_options')->orderBy('value')->get();
        $colorOptions = DB::table('color_options')->orderBy('value')->get();
        $warranties = Warranty::orderBy('warranty_month')->get();
        return view('admin.device.edit', compact('device', 'products', 'ramOptions', 'storageOptions', 'colorOptions', 'warranties'));
    }

    public function update(DeviceUpdateRequest $request, $id)
    {
        $this->requirePermission('devices.update');
        try {
            $device = Device::findOrFail($id);

            $existingImages = is_array($device->image) ? $device->image : ($device->image ? (array) $device->image : []);
            $newImages = $existingImages;

            if ($request->has('image') && is_array($request->image)) {
                $newImages = [];
                foreach ($request->image as $img) {
                    if (is_string($img) && preg_match('/^data:image\/(\w+);base64,/', $img, $matches)) {
                        $extension = $matches[1] ?? 'png';
                        $data = preg_replace('/^data:image\/(\w+);base64,/', '', $img);
                        $data = str_replace(' ', '+', $data);
                        $decoded = base64_decode($data);

                        $filename = uniqid('device_') . '.' . $extension;
                        Storage::disk('public')->put('devices/' . $filename, $decoded);
                        $newImages[] = $filename;
                    } elseif (is_string($img) && !empty($img)) {
                        $newImages[] = $img;
                    }
                }
            }

            $removed = array_diff($existingImages, $newImages);
            foreach ($removed as $file) {
                if ($file) {
                    Storage::disk('public')->delete('devices/' . $file);
                }
            }

            $colorOptionId = $request->color_option_id;
            if ($request->filled('new_color_name')) {
                $colorOptionId = VariantStock::getOrCreateColorOption($request->new_color_name, $request->new_color_value);
            }

            $variantId = VariantStock::findOrCreateVariantId(
                (int) $request->product_id,
                $request->ram_option_id ? (int) $request->ram_option_id : null,
                $request->storage_option_id ? (int) $request->storage_option_id : null,
                $colorOptionId
            );

            $device->update([
                'product_variant_id' => $variantId,
                'imei' => $request->imei,
                'warranty_id' => $request->warranty_id ?: null,
                'battery_percentage' => $request->battery_percentage,
                'condition_grade' => $request->condition_grade,
                'status' => $request->status ?? 'available',
                'purchase_price' => $request->purchase_price,
                'selling_price' => $request->selling_price,
                'image' => $newImages ?: null,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Device updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update device: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $this->requirePermission('devices.delete');
        $device = Device::findOrFail($id);

        if ($device->image && is_array($device->image)) {
            foreach ($device->image as $file) {
                Storage::disk('public')->delete('devices/' . $file);
            }
        }

        $device->delete();

        return response()->json([
            'status' => true,
            'message' => 'Device deleted successfully',
        ]);
    }
}
