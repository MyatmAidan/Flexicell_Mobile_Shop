<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Product;
use App\Models\Phone_model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductUpdateRequest;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function index()
    {
        return view('admin.product.index');
    }

    public function getList()
    {
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
        $phoneModels = Phone_model::with('brand', 'category')->get();
        return view('admin.product.create', compact('phoneModels'));
    }

    public function store(ProductCreateRequest $request)
    {
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
                    } elseif (is_string($img) && !empty($img)) {
                        $imageFiles[] = $img;
                    }
                }
            }

            $stockQuantity = (int) ($request->stock_quantity ?? 0);

            $product = Product::create([
                'phone_model_id' => $request->phone_model_id,
                'product_type' => $request->product_type,
                'selling_price' => $request->selling_price,
                'warranty_month' => $request->warranty_month,
                'image' => $imageFiles ?: null,
                'description' => $request->description,
                'stock_quantity' => $stockQuantity,
            ]);

            if ($stockQuantity > 0) {
                $devices = [];
                for ($i = 1; $i <= $stockQuantity; $i++) {
                    $devices[] = [
                        'product_id' => $product->id,
                        'imei' => 'PENDING-' . $product->id . '-' . $i . '-' . uniqid(),
                        'ram' => 'TBD',
                        'storage' => 'TBD',
                        'color' => 'TBD',
                        'battery_percentage' => 0,
                        'condition_grade' => 'TBD',
                        'status' => 'available',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                Device::insert($devices);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Product created successfully with ' . $stockQuantity . ' device(s). You can edit each device to add IMEI and other details.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create product: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $product = Product::with('phoneModel', 'devices')->findOrFail($id);
        $phoneModels = Phone_model::with('brand', 'category')->get();
        $products = Product::with('phoneModel.brand')->get();
        return view('admin.product.edit', compact('product', 'phoneModels', 'products'));
    }

    public function update(ProductUpdateRequest $request, $id)
    {
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
                        'ram' => 'TBD',
                        'storage' => 'TBD',
                        'color' => 'TBD',
                        'battery_percentage' => 0,
                        'condition_grade' => 'TBD',
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
