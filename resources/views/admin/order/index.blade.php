@extends('layouts.app')

@section('meta')
    <meta name="description" content="Order List">
    <meta name="keywords" content="Order, List, Flexicell">
@endsection

@section('title', 'Orders')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                        <h1 class="card-title h5 d-flex align-items-center gap-2 mb-0">
                            <iconify-icon icon="solar:cart-large-minimalistic-bold-duotone" class="fs-5"></iconify-icon>
                            Order List
                        </h1>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm" id="order-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Grand Total</th>
                                    <th>Payment Type</th>
                                    <th>Order Date</th>
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
        $('#order-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.order.getList') }}",
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'id', name: 'id' },
                { data: 'customer', name: 'customer.name' },
                { data: 'items_count', name: 'items_count', orderable: false },
                { data: 'grand_total', name: 'grand_total' },
                { data: 'order_status', name: 'order_status', orderable: false },
                { data: 'order_date', name: 'order_date' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            order: [[1, 'desc']],
        });
    });
</script>
@endsection
