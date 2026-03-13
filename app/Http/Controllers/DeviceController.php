<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Product;
use App\Http\Requests\DeviceCreateRequest;
use App\Http\Requests\DeviceUpdateRequest;
use Yajra\DataTables\Facades\DataTables;

class DeviceController extends Controller
{
    public function index()
    {
        return view('admin.device.index');
    }

    public function getList()
    {
        $devices = Device::with('product.phoneModel.brand');

        return DataTables::of($devices)
            ->addColumn('plus-icon', function () {
                return null;
            })
            ->addIndexColumn()
            ->addColumn('product_name', function ($device) {
                $product = $device->product;
                if (!$product || !$product->phoneModel) {
                    return '-';
                }
                return $product->phoneModel->model_name . ' (' . $product->product_type . ')';
            })
            ->addColumn('brand_name', function ($device) {
                return $device->product?->phoneModel?->brand?->brand_name ?? '-';
            })
            ->addColumn('action', function ($device) {
                return '<div class="action-btn" role="group">' .
                    '<button type="button" class="btn btn-sm mx-2 px-3 py-2 btn-primary edit-device-btn" data-id="' . $device->id . '" title="edit"><i class="fas fa-edit"></i></button>' .
                    '<a href="#" class="btn btn-danger btn-sm px-3 py-2 delete-btn" data-id="' . $device->id . '" title="delete"><i class="fa fa-trash-alt"></i></a>' .
                    '</div>';
            })
            ->rawColumns(['action', 'plus-icon'])
            ->make(true);
    }

    public function getData($id)
    {
        $device = Device::with('product.phoneModel.brand')->findOrFail($id);
        $products = Product::with('phoneModel.brand')->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'label' => ($p->phoneModel->model_name ?? '-') . ' (' . $p->product_type . ') - ' . ($p->phoneModel->brand->brand_name ?? ''),
            ];
        });

        return response()->json([
            'device' => [
                'id' => $device->id,
                'product_id' => $device->product_id,
                'imei' => $device->imei,
                'ram' => $device->ram,
                'storage' => $device->storage,
                'color' => $device->color,
                'battery_percentage' => $device->battery_percentage,
                'condition_grade' => $device->condition_grade,
                'status' => $device->status,
            ],
            'products' => $products,
        ]);
    }

    public function create()
    {
        $products = Product::with('phoneModel.brand')->get();
        return view('admin.device.create', compact('products'));
    }

    public function store(DeviceCreateRequest $request)
    {
        try {
            Device::create([
                'product_id' => $request->product_id,
                'imei' => $request->imei,
                'ram' => $request->ram,
                'storage' => $request->storage,
                'color' => $request->color,
                'battery_percentage' => $request->battery_percentage,
                'condition_grade' => $request->condition_grade,
                'status' => $request->status ?? 'available',
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
        $device = Device::with('product.phoneModel')->findOrFail($id);
        $products = Product::with('phoneModel.brand')->get();
        return view('admin.device.edit', compact('device', 'products'));
    }

    public function update(DeviceUpdateRequest $request, $id)
    {
        try {
            $device = Device::findOrFail($id);
            $device->update([
                'product_id' => $request->product_id,
                'imei' => $request->imei,
                'ram' => $request->ram,
                'storage' => $request->storage,
                'color' => $request->color,
                'battery_percentage' => $request->battery_percentage,
                'condition_grade' => $request->condition_grade,
                'status' => $request->status ?? 'available',
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
        $device = Device::findOrFail($id);
        $device->delete();

        return response()->json([
            'status' => true,
            'message' => 'Device deleted successfully',
        ]);
    }
}
