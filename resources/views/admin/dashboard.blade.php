@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Statistics Cards -->
    <div class="row">
        <!-- Total Revenue -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-dollar-sign fa-3x text-success"></i>
                        </div>
                        {{-- <div class="flex-grow-1 ms-3">
                            <h5 class="card-title">Total Revenue</h5>
                            <h3 class="mb-0">{{ number_format($totalRevenue, 2) }} MMK</h3>
                            <small class="text-muted">
                                This month: {{ number_format($revenueThisMonth, 2) }} MMK
                                @if ($revenueGrowth > 0)
                                    <span class="text-success">+{{ number_format($revenueGrowth, 1) }}%</span>
                                @else
                                    <span class="text-danger">{{ number_format($revenueGrowth, 1) }}%</span>
                                @endif
                            </small>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-shopping-cart fa-3x text-primary"></i>
                        </div>
                        {{-- <div class="flex-grow-1 ms-3">
                            <h5 class="card-title">Total Orders</h5>
                            <h3 class="mb-0">{{ number_format($totalOrders) }}</h3>
                            <small class="text-muted">
                                This month: {{ $ordersThisMonth }}
                                @if ($orderGrowth > 0)
                                    <span class="text-success">+{{ number_format($orderGrowth, 1) }}%</span>
                                @else
                                    <span class="text-danger">{{ number_format($orderGrowth, 1) }}%</span>
                                @endif
                            </small>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Customers -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    {{-- <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users fa-3x text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="card-title">Total Customers</h5>
                            <h3 class="mb-0">{{ number_format($totalCustomers) }}</h3>
                            <small class="text-muted">
                                New this month: {{ $newCustomersThisMonth }}
                            </small>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>

        <!-- Total Products -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    {{-- <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-mobile-alt fa-3x text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="card-title">Total Products</h5>
                            <h3 class="mb-0">{{ number_format($totalProducts) }}</h3>
                            <small class="text-muted">
                                Active: {{ $activeProducts }} | Low Stock: {{ $lowStockProducts }}
                            </small>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>

    <!-- Payment and Installment Statistics -->
    <div class="row mt-4">
        <!-- Payment Statistics -->
        <div class="col-lg-6">
            <div class="card">
                {{-- <div class="card-body">
                    <h5 class="card-title">Payment Statistics</h5>
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <h4 class="text-success">{{ number_format($completedPayments, 2) }}</h4>
                                <small class="text-muted">Completed Payments</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h4 class="text-warning">{{ number_format($pendingPayments, 2) }}</h4>
                                <small class="text-muted">Pending Payments</small>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <strong>Total Payment Amount: {{ number_format($totalPaymentAmount, 2) }} MMK</strong>
                    </div>
                </div> --}}
            </div>
        </div>

        <!-- Installment Statistics -->
        <div class="col-lg-6">
            <div class="card">
                {{-- <div class="card-body">
                    <h5 class="card-title">Installment Statistics</h5>
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <h4 class="text-warning">{{ number_format($upcomingPaymentsCount) }}</h4>
                                <small class="text-muted">Upcoming (7 days)</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h4 class="text-danger">{{ number_format($overduePaymentsCount) }}</h4>
                                <small class="text-muted">Overdue</small>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <strong>Pending Amount: {{ number_format($totalInstallmentAmount, 2) }} MMK</strong>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('admin.installment_payment.upcoming') }}" class="btn btn-primary btn-sm">View
                            Details</a>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>

    <!-- Charts and Tables -->
    <div class="row mt-4">
        <!-- Monthly Revenue Chart -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Monthly Revenue Trend</h5>
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- System Overview -->
        <div class="col-lg-4">
            <div class="card">
                {{-- <div class="card-body">
                    <h5 class="card-title">System Overview</h5>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Categories</span>
                            <span class="badge bg-primary">{{ $totalCategories }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Brands</span>
                            <span class="badge bg-info">{{ $totalBrands }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Payment Methods</span>
                            <span class="badge bg-success">{{ $totalPaymentMethods }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Out of Stock</span>
                            <span class="badge bg-danger">{{ $outOfStockProducts }}</span>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>

    <!-- Recent Orders and Top Products -->
    <div class="row mt-4">
        <!-- Recent Orders -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Recent Orders</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            {{-- <tbody>
                                @foreach ($recentOrders as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->customer->name ?? 'N/A' }}</td>
                                        <td>{{ number_format($order->total_amount, 0) }} MMK</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'processing' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody> --}}
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Top Selling Products</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Brand</th>
                                    <th>Orders</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            {{-- <tbody>
                                @foreach ($topProducts as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->category->name ?? 'N/A' }}</td>
                                        <td>{{ $product->brand->name ?? 'N/A' }}</td>
                                        <td>{{ $product->order_items_count }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $product->stock_quantity > 10 ? 'success' : ($product->stock_quantity > 0 ? 'warning' : 'danger') }}">
                                                {{ $product->stock_quantity }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody> --}}
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        
    </script>
@endsection
