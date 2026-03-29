<?php

namespace App\Http\Controllers;

use App\Http\Requests\PhoneModelCreateRequest;
use App\Http\Requests\PhoneModelUpdateRequest;
use App\Models\Brands;
use App\Models\Category;
use App\Models\Phone_model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PhoneModelController extends Controller
{
    public function index()
    {
        $this->requirePermission('phone_models.view');
        return view('admin.phone_model.index');
    }
    public function create()
    {
        $this->requirePermission('phone_models.create');
        $brands = Brands::all();
        $categories = Category::all();
        return view('admin.phone_model.create', compact('brands', 'categories'));
    }
    public function edit($id)
    {
        $this->requirePermission('phone_models.update');
        $phoneModel = Phone_model::findOrFail($id);
        $brands = Brands::all();
        $categories = Category::all();
        return view('admin.phone_model.edit', compact('phoneModel', 'brands', 'categories'));
    }
    public function show($id)
    {
        $this->requirePermission('phone_models.view');
        $phoneModel = Phone_model::with('brand', 'category')->findOrFail($id);
        return view('admin.phone_model.show', compact('phoneModel'));
    }


    public function store(PhoneModelCreateRequest $request)
    {
        $this->requirePermission('phone_models.create');
        try {
            $imageFiles = [];

            if ($request->has('image') && is_array($request->image)) {
                foreach ($request->image as $img) {
                    // base64 image
                    if (is_string($img) && preg_match('/^data:image\/(\w+);base64,/', $img, $matches)) {
                        $extension = $matches[1] ?? 'png';
                        $data = preg_replace('/^data:image\/(\w+);base64,/', '', $img);
                        $data = str_replace(' ', '+', $data);
                        $decoded = base64_decode($data);

                        $filename = uniqid('phone_') . '.' . $extension;
                        Storage::disk('public')->put('phone_model/' . $filename, $decoded);

                        $imageFiles[] = $filename;
                    } elseif (is_string($img) && !empty($img)) {
                        // already a filename
                        $imageFiles[] = $img;
                    }
                }
            }

            // Sync colors with color_options table
            if ($request->filled('available_color') && is_array($request->available_color)) {
                foreach ($request->available_color as $color) {
                    if (isset($color['name']) && isset($color['value'])) {
                        \App\Support\VariantStock::getOrCreateColorOption($color['name'], $color['value']);
                    }
                }
            }

            $phoneModel = Phone_model::create([
                'model_name'       => $request->name,
                'brand_id'         => $request->brand_id,
                'category_id'      => $request->category_id,
                'processor'        => $request->processor,
                'battery_capacity' => $request->battery_capacity,
                'release_year'     => $request->release_year,
                'description'      => $request->description ?: null,
                'available_color'  => $request->available_color ?: null,
                'image'            => $imageFiles ?: null,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Phone model created successfully',
                'id' => $phoneModel->id,
            ]);
        } catch (\Exception $e) {
            // Log and return safe error for AJAX clients
            Log::error('Failed to create phone model: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Failed to create phone model. Please check server logs.'
            ], 500);
        }
    }



    public function update(PhoneModelUpdateRequest $request, $id)
    {
        $this->requirePermission('phone_models.update');
        $phoneModel = Phone_model::findOrFail($id);

        $existingImages = is_array($phoneModel->image) ? $phoneModel->image : ($phoneModel->image ? (array) $phoneModel->image : []);
        $newImages = $existingImages; // default keep existing if no input

        if ($request->has('image') && is_array($request->image)) {
            $newImages = [];
            foreach ($request->image as $img) {
                if (is_string($img) && preg_match('/^data:image\/(\w+);base64,/', $img, $matches)) {
                    $extension = $matches[1] ?? 'png';
                    $data = preg_replace('/^data:image\/(\w+);base64,/', '', $img);
                    $data = str_replace(' ', '+', $data);
                    $decoded = base64_decode($data);

                    $filename = uniqid('phone_') . '.' . $extension;
                    Storage::disk('public')->put('phone_model/' . $filename, $decoded);

                    $newImages[] = $filename;
                } elseif (is_string($img) && !empty($img)) {
                    $newImages[] = $img; // existing filename
                }
            }
        }

        // delete removed images
        $removed = array_diff($existingImages, $newImages);
        foreach ($removed as $file) {
            if ($file) {
                Storage::disk('public')->delete('phone_model/' . $file);
            }
        }

        // Sync colors with color_options table
        if ($request->filled('available_color') && is_array($request->available_color)) {
            foreach ($request->available_color as $color) {
                if (isset($color['name']) && isset($color['value'])) {
                    \App\Support\VariantStock::getOrCreateColorOption($color['name'], $color['value']);
                }
            }
        }

        $phoneModel->update([
            'model_name'       => $request->name,
            'brand_id'         => $request->brand_id,
            'category_id'      => $request->category_id,
            'processor'        => $request->processor,
            'battery_capacity' => $request->battery_capacity,
            'release_year'     => $request->release_year,
            'description'      => $request->description ?: null,
            'available_color'  => $request->available_color ?: null,
            'image'            => $newImages ?: null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Phone model updated successfully'
        ]);
    }


    public function destroy($id)
    {
        $this->requirePermission('phone_models.delete');
        $phoneModel = Phone_model::findOrFail($id);
        $phoneModel->delete();

        return response()->json([
            'message' => 'Phone model deleted successfully'
        ]);
    }
    public function getList()
    {
        $this->requirePermission('phone_models.view');
        $phoneModels = Phone_model::with(['brand', 'category']);

        return datatables()->of($phoneModels)
            ->addColumn('plus-icon', function () {
                return '';
            })
            ->addIndexColumn()
            ->addColumn('brand_name', function ($phoneModel) {
                return $phoneModel->brand->brand_name ?? '-';
            })
            ->addColumn('category_name', function ($phoneModel) {
                return $phoneModel->category->category_name ?? '-';
            })
            ->addColumn('actions', function ($phoneModel) {
                $id = $phoneModel->id;

                return '
                            <div class="action-btn">
                                <a href="#" class="btn btn-sm mx-1 px-3 btn-info view-phone-model-btn" data-id="' . $id . '">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="#" class="btn btn-sm mx-1 px-3 btn-primary edit-phone-model-btn" data-id="' . $id . '">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" class="btn btn-sm px-3 btn-danger delete-btn" data-id="' . $id . '">
                                    <i class="fa fa-trash-alt"></i>
                                </a>
                            </div>
                        ';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
}
