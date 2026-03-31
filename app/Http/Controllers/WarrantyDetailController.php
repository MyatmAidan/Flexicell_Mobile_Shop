<?php

namespace App\Http\Controllers;

use App\Models\WarrantyDetail;
use Yajra\DataTables\Facades\DataTables;

class WarrantyDetailController extends Controller
{
    public function index()
    {
        $this->requirePermission('warranty_detail.view');

        return view('admin.warranty_detail.index');
    }

    public function getList()
    {
        $this->requirePermission('warranty_detail.view');

        $query = WarrantyDetail::query()
            ->with([
                'customer',
                'device.product.phoneModel.brand',
                'device.productVariant.storageOption',
                'warranty',
            ]);

        return DataTables::of($query)
            ->addColumn('plus-icon', fn () => null)
            ->addIndexColumn()
            ->addColumn('customer_name', fn (WarrantyDetail $wd) => $wd->customer?->name ?? '-')
            ->addColumn('customer_phone', fn (WarrantyDetail $wd) => $wd->customer?->phone ?? '-')
            ->addColumn('device_name', function (WarrantyDetail $wd) {
                $pm = $wd->device?->product?->phoneModel;
                $brand = $pm?->brand?->brand_name ?? '';
                $model = $pm?->model_name ?? '-';

                return trim("$brand $model");
            })
            ->addColumn('storage', fn (WarrantyDetail $wd) => $wd->device?->productVariant?->storageOption?->name ?? '-')
            ->addColumn('imei', fn (WarrantyDetail $wd) => $wd->device?->imei ?? '-')
            ->addColumn('warranty_months', fn (WarrantyDetail $wd) => ($wd->warranty?->warranty_month ?? '-') . ' months')
            ->addColumn('start', fn (WarrantyDetail $wd) => $wd->start_date?->format('Y-m-d') ?? '-')
            ->addColumn('end', fn (WarrantyDetail $wd) => $wd->end_date?->format('Y-m-d') ?? '-')
            ->addColumn('computed_status', function (WarrantyDetail $wd) {
                $status = $wd->computed_status;
                $badge = $status === 'active' ? 'success' : 'danger';

                return '<span class="badge bg-' . $badge . '">' . ucfirst($status) . '</span>';
            })
            ->rawColumns(['computed_status', 'plus-icon'])
            ->make(true);
    }
}
