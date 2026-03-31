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
                        <div class="flex-grow-1 ms-3">
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
                        </div>
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
                        <div class="flex-grow-1 ms-3">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Customers -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
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
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Devices -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-boxes fa-3x text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="card-title">Available Devices</h5>
                            <h3 class="mb-0">{{ number_format($totalDevices) }}</h3>
                            <small class="text-muted">
                                New: {{ $newDevices }} | 2nd Hand: {{ $secondHandDevices }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment and Installment Statistics -->
    <div class="row mt-4">
        <!-- Payment Statistics -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
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
                </div>
            </div>
        </div>

        <!-- Installment Statistics -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
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
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Tables -->
    <div class="row mt-4">
        <!-- Monthly Revenue Chart -->
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Monthly Revenue Trend</h5>
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Product Stock Doughnut Chart -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Product Stock Status</h5>
                    <div style="height: 260px; display: flex; justify-content: center; align-items: center;">
                        <canvas id="stockStatusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- System Overview -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">System Overview</h5>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Phone Models</span>
                            <span class="badge bg-primary">{{ $totalProducts }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Categories</span>
                            <span class="badge bg-secondary">{{ $totalCategories }}</span>
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
                </div>
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
                            <tbody>
                                @foreach ($recentOrders as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->user->name ?? 'N/A' }}</td>
                                        <td>{{ number_format($order->grand_total, 0) }} MMK</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $order->order_status == 'delivered' ? 'success' : ($order->order_status == 'processing' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($order->order_status) }}
                                            </span>
                                        </td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
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
                            <tbody>
                                @foreach ($topProducts as $product)
                                    <tr>
                                        <td>{{ $product->phoneModel->model_name ?? 'N/A' }}</td>
                                        <td>{{ $product->phoneModel->category->category_name ?? 'N/A' }}</td>
                                        <td>{{ $product->phoneModel->brand->brand_name ?? 'N/A' }}</td>
                                        <td>{{ $product->available_device_count ?? 0 }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $product->available_device_count > 10 ? 'success' : ($product->available_device_count > 0 ? 'warning' : 'danger') }}">
                                                {{ $product->available_device_count }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
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
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            
            const chartLabels = @json($chartLabels);
            const revenueData = @json($revenueData);
            const orderData = @json($orderData);
            
            const doughnutLabels = @json($doughnutLabels);
            const doughnutData = @json($doughnutData);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'Revenue (MMK)',
                            data: revenueData,
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Orders',
                            data: orderData,
                            type: 'line',
                            backgroundColor: 'rgba(255, 99, 132, 0.5)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 2,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Revenue (MMK)'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false,
                            },
                            title: {
                                display: true,
                                text: 'Orders'
                            }
                        }
                    }
                }
            });

            // Doughnut Chart for Product Stock Status
            const doughnutCtx = document.getElementById('stockStatusChart').getContext('2d');
            new Chart(doughnutCtx, {
                type: 'doughnut',
                data: {
                    labels: doughnutLabels,
                    datasets: [{
                        data: doughnutData,
                        backgroundColor: [
                            '#198754', // Active Stock (Success)
                            '#ffc107', // Low Stock (Warning)
                            '#dc3545'  // Out of Stock (Danger)
                        ],
                        borderWidth: 1,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                            }
                        }
                    },
                    cutout: '70%',
                }
            });
        });
    </script>
@endsection
