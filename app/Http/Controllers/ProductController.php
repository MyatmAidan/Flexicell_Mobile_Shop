<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Product;
use App\Models\Phone_model;
use App\Support\VariantStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\User;
use App\Models\SecondPhonePurchase;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function index()
    {
        $this->requirePermission('products.view');
        return view('admin.product.index');
    }

    public function getList()
    {
        $this->requirePermission('products.view');
        $products = Product::with('phoneModel.brand', 'phoneModel.category');

        return DataTables::of($products)
            ->addColumn('plus-icon', function () {
                return null;
            })
            ->addIndexColumn()
            ->addColumn('model_name', function ($product) {
                return $product->phoneModel->model_name ?? '-';
            })
            ->addColumn('brand_name', function ($product) {
                return $product->phoneModel->brand->brand_name ?? '-';
            })
            ->editColumn('image', function ($product) {
                $images = is_array($product->image) ? $product->image : [];
                $first = $images[0] ?? null;
                if (!$first) {
                    return '<span class="text-muted">No image</span>';
                }
                $url = asset('storage/products/' . $first);
                return '<img src="' . $url . '" alt="Product" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">';
            })
            ->addColumn('action', function ($product) {
                $editUrl = route('admin.product.edit', $product->id);
                return '<div class="action-btn" role="group">' .
                    '<a href="' . $editUrl . '" class="btn btn-sm mx-2 px-3 py-2 btn-primary" title="edit"><i class="fas fa-edit"></i></a>' .
                    '<a href="#" class="btn btn-danger btn-sm px-3 py-2 delete-btn" data-id="' . $product->id . '" title="delete"><i class="fa fa-trash-alt"></i></a>' .
                    '</div>';
            })
            ->rawColumns(['image', 'action', 'plus-icon'])
            ->make(true);
    }

    public function create()
    {
        $this->requirePermission('products.create');
        $phoneModels = Phone_model::with('brand', 'category')->get();
        $ramOptions = DB::table('ram_options')->orderBy('name')->get(['id', 'name', 'value']);
        $storageOptions = DB::table('storage_options')->orderBy('name')->get(['id', 'name', 'value']);
        $colorOptions = DB::table('color_options')->orderBy('name')->get(['id', 'name', 'value']);
        return view('admin.product.create', compact('phoneModels', 'ramOptions', 'storageOptions', 'colorOptions'));
    }

    public function store(ProductCreateRequest $request)
    {
        $this->requirePermission('products.create');
        try {
            DB::beginTransaction();

            $imageFiles = [];

            if ($request->has('image') && is_array($request->image)) {
                foreach ($request->image as $img) {
                    if (is_string($img) && preg_match('/^data:image\/(\w+);base64,/', $img, $matches)) {
                        $extension = $matches[1] ?? 'png';
                        $data = preg_replace('/^data:image\/(\w+);base64,/', '', $img);
                        $data = str_replace(' ', '+', $data);
                        $decoded = base64_decode($data);

                        $filename = uniqid('product_') . '.' . $extension;
                        Storage::disk('public')->put('products/' . $filename, $decoded);
                        $imageFiles[] = $filename;
                    } elseif (!empty($img)) {
                        $imageFiles[] = $img;
                    }
                }
            }

            $isSecondHand = $request->product_type === 'second hand';


            $product = Product::where('phone_model_id', $request->phone_model_id)
                ->where('product_type', $request->product_type)
                ->first();

            if ($product) {
                $product->increment('stock_quantity', $isSecondHand ? 1 : ($request->stock_quantity ?? 0));
            } else {
                $product = Product::create([
                    'phone_model_id' => $request->phone_model_id,
                    'product_type' => $request->product_type,
                    'warranty_month' => $request->warranty_month,
                    'image' => $imageFiles ?: null,
                    'description' => $request->description,
                    'stock_quantity' => $isSecondHand ? 1 : ($request->stock_quantity ?? 0),
                ]);
            }


            if ($isSecondHand) {

                $colorOptionId = $request->color_option_id;
                if ($request->filled('new_color_name')) {
                    $colorOptionId = \App\Support\VariantStock::getOrCreateColorOption($request->new_color_name, $request->new_color_value);
                }

                $purchase = SecondPhonePurchase::create([
                    'user_id' => \Illuminate\Support\Facades\Auth::id(),
                    'phone_model_id' => $request->phone_model_id,
                    'imei' => $request->imei,
                    'ram_option_id' => $request->ram_option_id,
                    'storage_option_id' => $request->storage_option_id,
                    'color_option_id' => $colorOptionId,
                    'image' => $imageFiles ?: null,
                    'condition_grade' => $request->condition_grade,
                    'battery_percentage' => $request->battery_percentage,
                    'buy_price' => $request->buy_price,
                    'purchase_at' => $request->purchase_at ?? now(),
                ]);

                $device = Device::create([
                    'product_id' => $product->id,
                    'second_purchase_id' => $purchase->id,
                    'imei' => $request->imei,
                    'ram_option_id' => $request->ram_option_id,
                    'storage_option_id' => $request->storage_option_id,
                    'color_option_id' => $colorOptionId,
                    'battery_percentage' => $request->battery_percentage,
                    'condition_grade' => $request->condition_grade,
                    'status' => 'available',
                    'purchase_price' => $request->buy_price,
                    'selling_price' => $request->selling_price,
                    'image' => $imageFiles ?: null,
                ]);

            } else {

                $stockQuantity = (int) ($request->stock_quantity ?? 0);

                if ($stockQuantity > 0) {
                    $devices = [];

                    for ($i = 1; $i <= $stockQuantity; $i++) {
                        $devices[] = [
                            'product_id' => $product->id,
                            'imei' => 'PENDING-' . $product->id . '-' . $i . '-' . uniqid(),
                            'ram_option_id' => null,
                            'storage_option_id' => null,
                            'color_option_id' => null,
                            'battery_percentage' => 0,
                            'condition_grade' => 'NEW',
                            'status' => 'available',
                            'selling_price' => $request->selling_price,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    Device::insert($devices);
                }

            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => $isSecondHand
                    ? 'Second-hand device purchased and added to inventory.'
                    : 'New product created with stock devices.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $this->requirePermission('products.update');
        $product = Product::with(['phoneModel', 'devices' => function($q) {
            $q->with(['ramOption', 'storageOption', 'colorOption']);
        }])->findOrFail($id);
        $phoneModels = Phone_model::with('brand', 'category')->get();
        $products = Product::with('phoneModel.brand')->get();
        $ramOptions = DB::table('ram_options')->orderBy('name')->get(['id', 'name', 'value']);
        $storageOptions = DB::table('storage_options')->orderBy('name')->get(['id', 'name', 'value']);
        $colorOptions = DB::table('color_options')->orderBy('name')->get(['id', 'name', 'value']);
        return view('admin.product.edit', compact('product', 'phoneModels', 'products', 'ramOptions', 'storageOptions', 'colorOptions'));
    }

    public function update(ProductUpdateRequest $request, $id)
    {
        $this->requirePermission('products.update');
        try {
            DB::beginTransaction();
            $product = Product::findOrFail($id);
            $existingImages = is_array($product->image) ? $product->image : ($product->image ? (array) $product->image : []);
            $newImages = $existingImages;

            if ($request->has('image') && is_array($request->image)) {
                $newImages = [];
                foreach ($request->image as $img) {
                    if (is_string($img) && preg_match('/^data:image\/(\w+);base64,/', $img, $matches)) {
                        $extension = $matches[1] ?? 'png';
                        $data = preg_replace('/^data:image\/(\w+);base64,/', '', $img);
                        $data = str_replace(' ', '+', $data);
                        $decoded = base64_decode($data);

                        $filename = uniqid('product_') . '.' . $extension;
                        Storage::disk('public')->put('products/' . $filename, $decoded);
                        $newImages[] = $filename;
                    } elseif (is_string($img) && !empty($img)) {
                        $newImages[] = $img;
                    }
                }
            }

            $removed = array_diff($existingImages, $newImages);
            foreach ($removed as $file) {
                if ($file) {
                    Storage::disk('public')->delete('products/' . $file);
                }
            }

            $newStockQuantity = (int) ($request->stock_quantity ?? 0);
            $currentDeviceCount = $product->devices()->count();

            if ($newStockQuantity > $currentDeviceCount) {
                $toAdd = $newStockQuantity - $currentDeviceCount;
                $devices = [];
                for ($i = 1; $i <= $toAdd; $i++) {
                    $devices[] = [
                        'product_id' => $product->id,
                        'imei' => 'PENDING-' . $product->id . '-' . ($currentDeviceCount + $i) . '-' . uniqid(),
                        'ram_option_id' => null,
                        'storage_option_id' => null,
                        'color_option_id' => null,
                        'battery_percentage' => 0,
                        'condition_grade' => 'NEW',
                        'status' => 'available',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                Device::insert($devices);
            }

            $product->update([
                'phone_model_id' => $request->phone_model_id,
                'product_type' => $request->product_type,
                'selling_price' => $request->selling_price,
                'warranty_month' => $request->warranty_month,
                'image' => $newImages ?: null,
                'description' => $request->description,
                'stock_quantity' => $newStockQuantity,
            ]);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Product updated successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update product: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $this->requirePermission('products.delete');
        $product = Product::findOrFail($id);

        if ($product->image && is_array($product->image)) {
            foreach ($product->image as $file) {
                Storage::disk('public')->delete('products/' . $file);
            }
        }

        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'Product deleted successfully',
        ]);
    }
}
