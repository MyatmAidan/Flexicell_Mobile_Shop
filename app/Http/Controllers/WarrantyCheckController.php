<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Device;
use App\Models\WarrantyDetail;
use Illuminate\Http\Request;

class WarrantyCheckController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();

        $imeiInput = old('imei', '');
        $device = null;
        $warrantyDetail = null;
        $notFound = false;

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'imei' => ['required', 'string', 'min:8', 'max:32'],
            ], [
                'imei.required' => 'Please enter an IMEI number.',
            ]);

            $raw = trim($validated['imei']);
            $normalized = preg_replace('/[\s\-]/', '', $raw);

            $device = Device::query()
                ->with([
                    'product.phoneModel.brand',
                    'product.phoneModel.category',
                    'ramOption',
                    'storageOption',
                    'colorOption',
                    'warranty',
                ])
                ->where('imei', 'not like', 'PENDING-%')
                ->where(function ($q) use ($raw, $normalized) {
                    $q->where('imei', $raw)
                        ->orWhere('imei', $normalized)
                        ->orWhereRaw(
                            "REPLACE(REPLACE(imei, ' ', ''), '-', '') = ?",
                            [$normalized]
                        );
                })
                ->first();

            if (!$device) {
                $notFound = true;
            } else {
                $warrantyDetail = WarrantyDetail::query()
                    ->with(['warranty', 'customer'])
                    ->where('device_id', $device->id)
                    ->latest('id')
                    ->first();

                $imeiInput = $raw;
            }
        }

        return view('warranty-check', compact(
            'categories',
            'device',
            'warrantyDetail',
            'notFound',
            'imeiInput'
        ));
    }
}
