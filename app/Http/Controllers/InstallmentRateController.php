<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstallmentRateCreateRequest;
use App\Http\Requests\InstallmentRateUpdateRequest;
use App\Models\InstallmentRate;
use Yajra\DataTables\Facades\DataTables;

class InstallmentRateController extends Controller
{
    public function index()
    {
        $this->requirePermission('installment_rate.manage');
        $installments = InstallmentRate::all();
        return view('admin.installment_rate.index', compact('installments'));
    }

    public function getList()
    {
        $this->requirePermission('installment_rate.manage');
        $installments = InstallmentRate::all();
        return DataTables::of($installments)
            ->addColumn('plus-icon', function () {
                return null;
            })
            ->addIndexColumn()
            ->editColumn('created_at', function ($c) {
                return $c->created_at
                    ? $c->created_at->format('Y-m-d')
                    : '-';
            })
            ->editColumn('month_option', function ($installment) {
                return $installment->month_option . ' months';
            })
            ->editColumn('rate', function ($installment) {
                return $installment->rate . '%';
            })

            ->addColumn('action', function ($installment) {
                $id = $installment->id;
                $editBtn = '<a href="#" class="btn btn-sm mx-2 px-3 edit-installment-btn py-2 btn-primary" title="edit" data-id="' . $id . '" data-installment-month="' . htmlspecialchars($installment->month_option, ENT_QUOTES) . '" data-installment-rate="' . htmlspecialchars($installment->rate, ENT_QUOTES) .  '"><i class="fas fa-edit"></i> </a>';
                $deleteBtn = '<a href="#" class="btn btn-danger btn-sm px-3 py-2 delete-btn" data-id="' . $id . '" title="delete"><i class="fa fa-trash-alt"></i> </a>';
                return '<div class="action-btn" role="group">' . $editBtn . ' ' . $deleteBtn . '</div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create()
    {
        $this->requirePermission('installment_rate.manage');
        return view('admin.installment_rate.create');
    }

    public function store(InstallmentRateCreateRequest $request)
    {
        $this->requirePermission('installment_rate.manage');
        InstallmentRate::Create([
            'month_option' => $request->installment_month,
            'rate' => $request->installment_rate,
        ]);
        return response()->json([
            'message' => 'Installment plan created successfully'
        ]);
    }

    public function edit($id)
    {
        $this->requirePermission('installment_rate.manage');
        $installment = InstallmentRate::findOrFail($id);
        return view('admin.installment_rate.edit', compact('installment'));
    }

    public function update(InstallmentRateUpdateRequest $request, $id)
    {
        $this->requirePermission('installment_rate.manage');
        $installment = InstallmentRate::findOrFail($id);
        $installment->update([
            'month_option' => $request->installment_month,
            'rate' => $request->installment_rate,
        ]);

        return response()->json([
            'message' => 'Installment plan updated successfully'
        ]);
    }

    public function destroy($id)
    {
        $this->requirePermission('installment_rate.manage');
        $installment = InstallmentRate::find($id);

        if (!$installment) {
            return response()->json([
                'status' => false,
                'message' => 'Installment plan not found.'
            ], 404);
        }

        $installment->delete();

        return response()->json([
            'status' => true,
            'message' => 'Installment plan deleted successfully.'
        ]);
    }
}
