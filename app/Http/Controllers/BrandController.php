<?php

namespace App\Http\Controllers;

use App\Models\Brands;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\BrandCreateRequest;
use App\Http\Requests\BrandUpdateRequest;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brands::all();
        return view('admin.brand.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brand.create');
    }

    public function store(BrandCreateRequest $request)
    {
        $request->validate([
            'brand_name' => 'required|string|max:255',
            'logo' => 'nullable|image',
        ]);

        Brands::create([
            'brand_name' => $request->brand_name,
            'logo' => $request->file('logo')?->store('brands', 'public'),
        ]);

        return response()->json([
            'message' => 'Brand created successfully'
        ]);
    }


    public function getList()
    {
        $brands = Brands::all();
        return DataTables::of($brands)
            ->addColumn('plus-icon', function ($brand) {
                return null;
            })
            ->addIndexColumn()
            ->editColumn('logo', function ($brand) {
                return '<img src="' . $brand->logoUrl() . '" alt="' . $brand->brand_name . '" class="img-thumbnail" style="width: 50px; height: 50px;">';
            })
            ->addColumn('action', function ($brand) {
                $id = $brand->id;
                $logoName = $brand->logo ? basename($brand->logo) : '';
                $editBtn = '<a href="#" class="btn btn-sm mx-2 px-3 edit-brand-btn py-2 btn-primary" title="edit" data-id="' . $id . '" data-name="' . htmlspecialchars($brand->brand_name, ENT_QUOTES) . '" data-logo="' . $brand->logoUrl() . '" data-logo-name="' . $logoName . '"><i class="fas fa-edit"></i> </a>';
                $deleteBtn = '<a href="#" class="btn btn-danger btn-sm px-3 py-2 delete-btn" data-id="' . $id . '" title="delete"><i class="fa fa-trash-alt"></i> </a>';
                return '<div class="action-btn" role="group">' . $editBtn . ' ' . $deleteBtn . '</div>';
            })

            ->rawColumns(['logo', 'action', 'plus-icon'])
            ->make(true);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $brand = Brands::findOrFail($id);
        return view('admin.brand.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BrandUpdateRequest $request, string $id)
    {
        try {
            $brand = Brands::findOrFail($id);
            $data = [
                'brand_name' => $request->brand_name,
            ];

            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($brand->logo) {
                    Storage::disk('public')->delete($brand->logo);
                }
                
                // Store the new logo and save the path
                $data['logo'] = $request->file('logo')->store('brands', 'public');
            }

            $brand->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Brand updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update brand: ' . $e->getMessage()
            ], 500);
        }
    }



    public function destroy(string $id)
    {
        $brand = Brands::findOrFail($id);

        // Delete logo from storage if exists
        if ($brand->logo) {
            Storage::disk('public')->delete('brands/' . $brand->logo);
        }

        $brand->delete();

        return response()->json([
            'status' => true,
            'message' => 'Brand deleted successfully'
        ]);
    }

    protected function storeLogo($base64Image, $brandName)
    {

        $folderPath = "brands/";
        $parts = explode(";base64,", $base64Image);
        $imageParts = explode("image/", $parts[0]);
        $imageType = $imageParts[1];
        $imageBase64 = base64_decode($parts[1]);

        $fileName = $brandName . '_' . time() . '.' . $imageType;
        $filePath = $folderPath . $fileName;

        Storage::disk('public')->put($filePath, $imageBase64);

        return $filePath;
    }

}
