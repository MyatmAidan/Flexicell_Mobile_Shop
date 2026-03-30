@extends('layouts.app')

@section('meta')
    <meta name="description" content="Warranty Details">
    <meta name="keywords" content="Warranty, Details, Flexicell">
@endsection

@section('title', 'Warranty Details')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                        <h1 class="card-title h5 d-flex align-items-center gap-2 mb-0">
                            Warranty Details
                        </h1>
                    </div>
                    <div class="">
                        <table class="table table-hover table-sm" id="datatable">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Phone</th>
                                    <th>Device</th>
                                    <th>Storage</th>
                                    <th>IMEI</th>
                                    <th>Warranty</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
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
        $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.warranty_detail.getList') }}",
            columns: [
                { data: 'plus-icon', name: 'plus-icon', className: 'dt-control', orderable: false, searchable: false },
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'customer_name', name: 'customer_name' },
                { data: 'customer_phone', name: 'customer_phone' },
                { data: 'device_name', name: 'device_name', orderable: false },
                { data: 'storage', name: 'storage', orderable: false },
                { data: 'imei', name: 'imei', orderable: false },
                { data: 'warranty_months', name: 'warranty_months', orderable: false },
                { data: 'start', name: 'start_date' },
                { data: 'end', name: 'end_date' },
                { data: 'computed_status', name: 'computed_status', orderable: false, searchable: false },
            ],
            order: [[8, 'desc']],
        });
    });
</script>
@endsection
