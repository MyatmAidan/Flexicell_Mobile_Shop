<?php

namespace App\Http\Controllers;

use App\Models\Brands;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Payment;
use App\Models\InstallmentPayment;
use App\Models\Device;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        // Revenue
        $totalRevenue = Order::sum('grand_total');
        $revenueThisMonth = Order::where('created_at', '>=', $startOfMonth)->sum('grand_total');
        $revenueLastMonth = Order::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->sum('grand_total');
        
        $revenueGrowth = 0;
        if ($revenueLastMonth > 0) {
            $revenueGrowth = (($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100;
        } elseif ($revenueThisMonth > 0) {
            $revenueGrowth = 100;
        }

        // Available Devices
        $totalDevices = Device::where('status', 'available')->whereNull('order_id')->count();
        $newDevices = Device::where('status', 'available')->whereNull('order_id')
            ->whereHas('product', fn($q) => $q->where('product_type', 'new'))
            ->count();
        $secondHandDevices = Device::where('status', 'available')->whereNull('order_id')
            ->whereHas('product', fn($q) => $q->where('product_type', 'second hand'))
            ->count();

        // Orders
        $totalOrders = Order::count();
        $ordersThisMonth = Order::where('created_at', '>=', $startOfMonth)->count();
        $ordersLastMonth = Order::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();

        $orderGrowth = 0;
        if ($ordersLastMonth > 0) {
            $orderGrowth = (($ordersThisMonth - $ordersLastMonth) / $ordersLastMonth) * 100;
        } elseif ($ordersThisMonth > 0) {
            $orderGrowth = 100;
        }

        // Customers (users with customer role)
        $totalCustomers = User::whereHas('assignedRole', fn ($q) => $q->where('code', 'user'))->count();
        $newCustomersThisMonth = User::whereHas('assignedRole', fn ($q) => $q->where('code', 'user'))
            ->where('created_at', '>=', $startOfMonth)
            ->count();

        // Products
        $totalProducts = Product::count();
        $activeProducts = Product::where('stock_quantity', '>', 0)->count();
        $lowStockProducts = Product::where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 5)->count();
        $outOfStockProducts = Product::where('stock_quantity', 0)->count();

        // System Overview
        $totalCategories = Category::count();
        $totalBrands = Brands::count();
        $totalPaymentMethods = 2; // Default, placeholder if PaymentMethod doesn't exist

        // Payments
        $completedPayments = Payment::whereIn('status', ['paid', 'completed'])->sum('amount') ?? 0;
        $pendingPayments = Payment::where('status', 'pending')->sum('amount') ?? 0;
        $totalPaymentAmount = Payment::sum('amount') ?? 0;

        // Installments
        $upcomingPaymentsCount = InstallmentPayment::where('status', 'pending')->where('paid_date', '>=', Carbon::now())->where('paid_date', '<', Carbon::now()->addDays(7))->count() ?? 0;
        $overduePaymentsCount = InstallmentPayment::where('status', 'pending')->where('paid_date', '<', Carbon::now())->count() ?? 0;
        $totalInstallmentAmount = \App\Models\Installment::sum('remaining_amount') ?? 0;

        // Recent Orders
        $recentOrders = Order::with('user')->orderBy('created_at', 'desc')->take(5)->get();

        // Top Products
        // For simplicity, we can fetch products with the most stock or add a simple relation if order_items logic is tricky
        $topProducts = Product::with(['phoneModel.brand', 'phoneModel.category'])
            ->withCount('devices') // Assuming devices mean orders/sales in this structure or we could just use stock for now
            ->orderBy('devices_count', 'desc')
            ->take(5)
            ->get();

        // Chart Data (Last 6 Months Revenue and Orders)
        $chartLabels = [];
        $revenueData = [];
        $orderData = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthStart = $now->copy()->subMonths($i)->startOfMonth();
            $monthEnd = $now->copy()->subMonths($i)->endOfMonth();

            $chartLabels[] = $monthStart->format('M Y');
            $revenueData[] = Order::whereBetween('created_at', [$monthStart, $monthEnd])->sum('grand_total');
            $orderData[] = Order::whereBetween('created_at', [$monthStart, $monthEnd])->count();
        }

        // Doughnut Chart Data (Product Stock Status)
        $stockStatusCounts = [
            'Active Stock' => $activeProducts,
            'Low Stock' => $lowStockProducts,
            'Out of Stock' => $outOfStockProducts,
        ];
        $doughnutLabels = array_keys($stockStatusCounts);
        $doughnutData = array_values($stockStatusCounts);

        return view('admin.dashboard', compact(
            'totalRevenue', 'revenueThisMonth', 'revenueGrowth',
            'totalOrders', 'ordersThisMonth', 'orderGrowth',
            'totalCustomers', 'newCustomersThisMonth',
            'totalProducts', 'activeProducts', 'lowStockProducts', 'outOfStockProducts',
            'totalDevices', 'newDevices', 'secondHandDevices',
            'totalCategories', 'totalBrands', 'totalPaymentMethods',
            'completedPayments', 'pendingPayments', 'totalPaymentAmount',
            'upcomingPaymentsCount', 'overduePaymentsCount', 'totalInstallmentAmount',
            'recentOrders', 'topProducts',
            'chartLabels', 'revenueData', 'orderData',
            'doughnutLabels', 'doughnutData'
        ));
    }
}
