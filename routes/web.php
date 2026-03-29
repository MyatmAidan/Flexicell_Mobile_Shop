<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BlogFrontController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DirectSaleController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InstallmentRateController;
use App\Http\Controllers\PhoneModelController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPermissionController;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public storefront
Route::get('/', function () {
    $categories = Category::all();

    $baseQuery = Product::with('phoneModel.brand', 'phoneModel.category');

    $new_products = (clone $baseQuery)->orderByDesc('created_at')->take(12)->get();
    $popular_products = (clone $baseQuery)->orderByDesc('stock_quantity')->take(12)->get();
    $best_sellers = Product::with('phoneModel.brand', 'phoneModel.category')
        ->withCount('devices')
        ->orderByDesc('devices_count')
        ->take(12)
        ->get();

    return view('index', compact('categories', 'new_products', 'popular_products', 'best_sellers'));
})->name('home');

Route::get('/products', function () {
    $categories = Category::all();
    return view('products', compact('categories'));
})->name('products');

Route::get('/about', function () {
    $categories = Category::all();
    return view('about', compact('categories'));
})->name('about');

Route::get('/contact', function () {
    $categories = Category::all();
    return view('contact', compact('categories'));
})->name('contact');

Route::get('/help', function () {
    $categories = Category::all();
    return view('help', compact('categories'));
})->name('help');

Route::get('/trade-in', function () {
    $categories = Category::all();
    return view('tradeInInfo', compact('categories'));
})->name('trade_in');

// Blog (public)
Route::get('/blog', [BlogFrontController::class, 'index'])->name('blogs.index');
Route::get('/blog/{blog}', [BlogFrontController::class, 'show'])->name('blogs.show');


Route::get('/trade-in/estimate', function () {
    $categories = Category::all();
    return view('tradeInEstimate', compact('categories'));
})->name('trade_in.estimate');

Route::get('/products/search', function (Request $request) {
    $categories = Category::all();
    $brands = \App\Models\Brand::all();

    $query = Product::with('phoneModel.brand', 'phoneModel.category');

    if ($request->filled('category_id')) {
        $categoryId = $request->input('category_id');
        $query->whereHas('phoneModel', function ($q) use ($categoryId) {
            $q->where('category_id', $categoryId);
        });
    }

    if ($request->filled('brand_id')) {
        $brandId = $request->input('brand_id');
        $query->whereHas('phoneModel', function ($q) use ($brandId) {
            $q->where('brand_id', $brandId);
        });
    }

    if ($request->filled('query')) {
        $term = $request->input('query');
        $query->whereHas('phoneModel', function ($q) use ($term) {
            $q->where('model_name', 'like', '%' . $term . '%')
                ->orWhereHas('brand', function ($qb) use ($term) {
                    $qb->where('brand_name', 'like', '%' . $term . '%');
                });
        });
    }

    $products = $query->orderByDesc('created_at')->get();

    return view('search', compact('products', 'categories', 'brands'));
})->name('products.search');

Route::get('/products/{product}', function (Product $product) {
    $categories = Category::all();
    $product->load('phoneModel.brand', 'phoneModel.category');

    return view('productDetails', compact('product', 'categories'));
})->name('products.show');

Route::post('/products/update-view', function (Request $request) {
    return response()->noContent();
})->name('products.updateView');

// Auth (guest-only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Authenticated-only (any logged-in user)
Route::middleware('authCheck')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::middleware(['authCheck', 'adminAuth'])->name('admin.')->group(function () {

    Route::prefix('admin')->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // ---- User Management (superadmin only) --------------------------
        Route::middleware('permission:users.manage')->prefix('user')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('user.index');
            Route::get('/list', [UserController::class, 'getList'])->name('user.getList');
            Route::get('/create', [UserController::class, 'create'])->name('user.create');
            Route::post('/', [UserController::class, 'store'])->name('user.store');
            Route::get('/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
            Route::put('/update/{id}', [UserController::class, 'update'])->name('user.update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('user.destroy');
            // Custom per-user permissions
            Route::get('/{id}/permissions', [UserPermissionController::class, 'edit'])->name('user.permissions.edit');
            Route::put('/{id}/permissions', [UserPermissionController::class, 'update'])->name('user.permissions.update');
        });

        // ---- Role Management (superadmin only) --------------------------
        Route::middleware('permission:roles.manage')->prefix('role')->group(function () {
            Route::get('/', [\App\Http\Controllers\RoleController::class, 'index'])->name('role.index');
            Route::get('/list', [\App\Http\Controllers\RoleController::class, 'getList'])->name('role.getList');
            Route::get('/create', [\App\Http\Controllers\RoleController::class, 'create'])->name('role.create');
            Route::post('/', [\App\Http\Controllers\RoleController::class, 'store'])->name('role.store');
            Route::get('/edit/{id}', [\App\Http\Controllers\RoleController::class, 'edit'])->name('role.edit');
            Route::put('/update/{id}', [\App\Http\Controllers\RoleController::class, 'update'])->name('role.update');
            Route::delete('/{role}', [\App\Http\Controllers\RoleController::class, 'destroy'])->name('role.destroy');
        });

        // ---- Brands (superadmin + manager) ------------------------------
        Route::middleware('permission:brand.manage')->prefix('brand')->group(function () {
            Route::get('/', [BrandController::class, 'index'])->name('brand.index');
            Route::get('/list', [BrandController::class, 'getList'])->name('brand.getList');
            Route::get('/create', [BrandController::class, 'create'])->name('brand.create');
            Route::post('/', [BrandController::class, 'store'])->name('brand.store');
            Route::get('/edit/{id}', [BrandController::class, 'edit'])->name('brand.edit');
            Route::put('/update/{id}', [BrandController::class, 'update'])->name('brand.update');
            Route::delete('/{brand}', [BrandController::class, 'destroy'])->name('brand.destroy');
        });

        // ---- Categories (superadmin + manager) --------------------------
        Route::middleware('permission:category.manage')->prefix('category')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('category.index');
            Route::get('/list', [CategoryController::class, 'getList'])->name('category.getList');
            Route::get('/create', [CategoryController::class, 'create'])->name('category.create');
            Route::post('/', [CategoryController::class, 'store'])->name('category.store');
            Route::get('/edit/{id}', [CategoryController::class, 'edit'])->name('category.edit');
            Route::put('/update/{id}', [CategoryController::class, 'update'])->name('category.update');
            Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('category.destroy');
        });

        // ---- Products (superadmin + manager) ----------------------------
        Route::middleware('permission:product.manage')->prefix('product')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('product.index');
            Route::get('/list', [ProductController::class, 'getList'])->name('product.getList');
            Route::get('/create', [ProductController::class, 'create'])->name('product.create');
            Route::post('/', [ProductController::class, 'store'])->name('product.store');
            Route::get('/edit/{id}', [ProductController::class, 'edit'])->name('product.edit');
            Route::put('/update/{id}', [ProductController::class, 'update'])->name('product.update');
            Route::delete('/{product}', [ProductController::class, 'destroy'])->name('product.destroy');
        });

        // ---- Phone Models (superadmin + manager) ------------------------
        Route::middleware('permission:phone_model.manage')->prefix('phone-model')->group(function () {
            Route::get('/', [PhoneModelController::class, 'index'])->name('phone_model.index');
            Route::get('/list', [PhoneModelController::class, 'getList'])->name('phone_model.getList');
            Route::get('/create', [PhoneModelController::class, 'create'])->name('phone_model.create');
            Route::post('/', [PhoneModelController::class, 'store'])->name('phone_model.store');
            Route::get('/edit/{id}', [PhoneModelController::class, 'edit'])->name('phone_model.edit');
            Route::put('/update/{id}', [PhoneModelController::class, 'update'])->name('phone_model.update');
            Route::get('/show/{id}', [PhoneModelController::class, 'show'])->name('phone_model.show');
            Route::delete('/{phoneModel}', [PhoneModelController::class, 'destroy'])->name('phone_model.destroy');
        });

        // ---- Installment Rates (superadmin + manager) -------------------
        Route::middleware('permission:installment_rate.manage')->prefix('installment_rate')->group(function () {
            Route::get('/', [InstallmentRateController::class, 'index'])->name('installment_rate.index');
            Route::get('/list', [InstallmentRateController::class, 'getList'])->name('installment_rate.getList');
            Route::get('/create', [InstallmentRateController::class, 'create'])->name('installment_rate.create');
            Route::post('/', [InstallmentRateController::class, 'store'])->name('installment_rate.store');
            Route::get('/edit/{id}', [InstallmentRateController::class, 'edit'])->name('installment_rate.edit');
            Route::put('/update/{id}', [InstallmentRateController::class, 'update'])->name('installment_rate.update');
            Route::delete('/{installment_rate}', [InstallmentRateController::class, 'destroy'])->name('installment_rate.destroy');
        });

        // ---- Devices (all roles) ----------------------------------------
        Route::middleware('permission:device.manage')->prefix('device')->group(function () {
            Route::get('/', [DeviceController::class, 'index'])->name('device.index');
            Route::get('/list', [DeviceController::class, 'getList'])->name('device.getList');
            Route::get('/data/{id}', [DeviceController::class, 'getData'])->name('device.getData');
            Route::get('/create', [DeviceController::class, 'create'])->name('device.create');
            Route::post('/', [DeviceController::class, 'store'])->name('device.store');
            Route::get('/edit/{id}', [DeviceController::class, 'edit'])->name('device.edit');
            Route::put('/update/{id}', [DeviceController::class, 'update'])->name('device.update');
            Route::delete('/{device}', [DeviceController::class, 'destroy'])->name('device.destroy');
        });

        // ---- Direct Sale / POS (all roles) ------------------------------
        Route::middleware('permission:direct_sale.manage')->prefix('direct-sale')->group(function () {
            Route::get('/', [DirectSaleController::class, 'index'])->name('direct_sale.index');
            Route::get('/products', [DirectSaleController::class, 'searchProducts'])->name('direct_sale.products');
            Route::get('/devices', [DirectSaleController::class, 'searchDevices'])->name('direct_sale.devices');
            Route::get('/variants/{product}', [DirectSaleController::class, 'variants'])->name('direct_sale.variants');
            Route::get('/variants-stock', [DirectSaleController::class, 'checkVariantStock'])->name('direct_sale.variants_stock');
            Route::post('/checkout', [DirectSaleController::class, 'checkout'])->name('direct_sale.checkout');
            Route::get('/receipt/{order}', [DirectSaleController::class, 'receipt'])->name('direct_sale.receipt');
        });

        // ---- Orders (all roles, view only for staff) --------------------
        Route::middleware('permission:order.view')->prefix('order')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('order.index');
            Route::get('/list', [OrderController::class, 'getList'])->name('order.getList');
            Route::get('/{id}', [OrderController::class, 'show'])->name('order.show');
            Route::get('/{id}/receipt', [OrderController::class, 'receipt'])->name('order.receipt');
        });

        // ---- Installments (all roles) -----------------------------------
        Route::middleware('permission:installment.manage')->prefix('installment')->group(function () {
            Route::get('/', [InstallmentController::class, 'index'])->name('installment.index');
            Route::get('/list', [InstallmentController::class, 'getList'])->name('installment.getList');
            Route::post('/payment/{paymentId}/mark-paid', [InstallmentController::class, 'markPaid'])->name('installment.markPaid');
            Route::get('/{id}', [InstallmentController::class, 'show'])->name('installment.show');
        });

        // ---- Blogs (superadmin + manager) -------------------------------
        Route::middleware('permission:blog.manage')->prefix('blogs')->group(function () {
            Route::get('/', [BlogController::class, 'index'])->name('blogs.index');
            Route::get('/list', [BlogController::class, 'getList'])->name('blogs.getList');
            Route::get('/create', [BlogController::class, 'create'])->name('blogs.create');
            Route::post('/', [BlogController::class, 'store'])->name('blogs.store');
            Route::get('/{blog}', [BlogController::class, 'show'])->name('blogs.show');
            Route::get('/{blog}/edit', [BlogController::class, 'edit'])->name('blogs.edit');
            Route::put('/{blog}', [BlogController::class, 'update'])->name('blogs.update');
            Route::delete('/{blog}', [BlogController::class, 'destroy'])->name('blogs.destroy');
        });
    });
});
