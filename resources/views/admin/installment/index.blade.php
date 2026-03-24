@extends('layouts.app')

@section('meta')
    <meta name="description" content="Installment List">
    <meta name="keywords" content="Installment, List, Flexicell">
@endsection

@section('title', 'Installments')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                        <h1 class="card-title h5 d-flex align-items-center gap-2 mb-0">
                            <iconify-icon icon="solar:bill-list-bold-duotone" class="fs-5"></iconify-icon>
                            Installment List
                        </h1>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm" id="installment-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Install. ID</th>
                                    <th>Order</th>
                                    <th>Customer</th>
                                    <th>Total Amount</th>
                                    <th>Down Payment</th>
                                    <th>Monthly</th>
                                    <th>Months</th>
                                    <th>Start Date</th>
                                    <th>Progress</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    $(document).ready(function () {
        $('#installment-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.installment.getList') }}",
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'id', name: 'id' },
                { data: 'order_id', name: 'order_id', orderable: false },
                { data: 'customer', name: 'customer', orderable: false },
                { data: 'total_amount', name: 'total_amount' },
                { data: 'down_payment', name: 'down_payment' },
                { data: 'monthly_amount', name: 'monthly_amount' },
                { data: 'months', name: 'months' },
                { data: 'start_date', name: 'start_date' },
                { data: 'progress', name: 'progress', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            order: [[1, 'desc']],
        });
    });
</script>
@endsection
