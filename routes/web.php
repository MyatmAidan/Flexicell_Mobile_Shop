<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BlogFrontController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\DirectSaleController;
use App\Http\Controllers\PhoneModelController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public storefront
Route::get('/', function () {
    $categories = Category::all();

    $baseQuery = Product::with('phoneModel.brand', 'phoneModel.category');

    $new_products = (clone $baseQuery)->orderByDesc('created_at')->take(12)->get();
    $popular_products = (clone $baseQuery)->orderByDesc('stock_quantity')->take(12)->get();
    $best_sellers = (clone $baseQuery)->orderByDesc('selling_price')->take(12)->get();

    return view('index', compact('categories', 'new_products', 'popular_products', 'best_sellers'));
})->name('home');

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

    $query = Product::with('phoneModel.brand', 'phoneModel.category');

    if ($request->filled('category_id')) {
        $categoryId = $request->input('category_id');
        $query->whereHas('phoneModel', function ($q) use ($categoryId) {
            $q->where('category_id', $categoryId);
        });
    }

    if ($request->filled('query')) {
        $term = $request->input('query');
        $query->whereHas('phoneModel', function ($q) use ($term) {
            $q->where('model_name', 'like', '%' . $term . '%');
        });
    }

    $products = $query->orderByDesc('created_at')->get();

    return view('search', compact('products', 'categories'));
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

Route::middleware('authCheck')->name('admin.')->group(function () {

    Route::prefix('admin')->group(function () {

        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        //user routes
        Route::prefix('user')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('user.index');
            Route::get('/list', [UserController::class, 'getList'])->name('user.getList');
            Route::get('/create', [UserController::class, 'create'])->name('user.create');
            Route::post('/', [UserController::class, 'store'])->name('user.store');
            Route::get('/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
            Route::put('/update/{id}', [UserController::class, 'update'])->name('user.update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('user.destroy');
        });

        //brand routes
        Route::prefix('brand')->group(function () {
            Route::get('/', [BrandController::class, 'index'])->name('brand.index');
            Route::get('/list', [BrandController::class, 'getList'])->name('brand.getList');
            Route::get('/create', [BrandController::class, 'create'])->name('brand.create');
            Route::post('/', [BrandController::class, 'store'])->name('brand.store');
            Route::get('/edit/{id}', [BrandController::class, 'edit'])->name('brand.edit');
            Route::put('/update/{id}', [BrandController::class, 'update'])->name('brand.update');
            Route::delete('/{brand}', [BrandController::class, 'destroy'])->name('brand.destroy');
        });

        //category routes
        Route::prefix('category')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('category.index');
            Route::get('/list', [CategoryController::class, 'getList'])->name('category.getList');
            Route::get('/create', [CategoryController::class, 'create'])->name('category.create');
            Route::post('/', [CategoryController::class, 'store'])->name('category.store');
            Route::get('/edit/{id}', [CategoryController::class, 'edit'])->name('category.edit');
            Route::put('/update/{id}', [CategoryController::class, 'update'])->name('category.update');
            Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('category.destroy');
        });

        //product routes
        Route::prefix('product')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('product.index');
            Route::get('/list', [ProductController::class, 'getList'])->name('product.getList');
            Route::get('/create', [ProductController::class, 'create'])->name('product.create');
            Route::post('/', [ProductController::class, 'store'])->name('product.store');
            Route::get('/edit/{id}', [ProductController::class, 'edit'])->name('product.edit');
            Route::put('/update/{id}', [ProductController::class, 'update'])->name('product.update');
            Route::delete('/{product}', [ProductController::class, 'destroy'])->name('product.destroy');
        });

        //device routes
        Route::prefix('device')->group(function () {
            Route::get('/', [DeviceController::class, 'index'])->name('device.index');
            Route::get('/list', [DeviceController::class, 'getList'])->name('device.getList');
            Route::get('/data/{id}', [DeviceController::class, 'getData'])->name('device.getData');
            Route::get('/create', [DeviceController::class, 'create'])->name('device.create');
            Route::post('/', [DeviceController::class, 'store'])->name('device.store');
            Route::get('/edit/{id}', [DeviceController::class, 'edit'])->name('device.edit');
            Route::put('/update/{id}', [DeviceController::class, 'update'])->name('device.update');
            Route::delete('/{device}', [DeviceController::class, 'destroy'])->name('device.destroy');
        });

        //phone model routes
        Route::prefix('phone-model')->group(function () {
            Route::get('/', [PhoneModelController::class, 'index'])->name('phone_model.index');
            Route::get('/list', [PhoneModelController::class, 'getList'])->name('phone_model.getList');
            Route::get('/create', [PhoneModelController::class, 'create'])->name('phone_model.create');
            Route::post('/', [PhoneModelController::class, 'store'])->name('phone_model.store');
            Route::get('/edit/{id}', [PhoneModelController::class, 'edit'])->name('phone_model.edit');
            Route::put('/update/{id}', [PhoneModelController::class, 'update'])->name('phone_model.update');
            Route::get('/show/{id}', [PhoneModelController::class, 'show'])->name('phone_model.show');
            Route::delete('/{phoneModel}', [PhoneModelController::class, 'destroy'])->name('phone_model.destroy');
        });

        //direct sale (POS checkout)
        Route::prefix('direct-sale')->group(function () {
            Route::get('/', [DirectSaleController::class, 'index'])->name('direct_sale.index');
            Route::get('/products', [DirectSaleController::class, 'searchProducts'])->name('direct_sale.products');
            Route::get('/devices', [DirectSaleController::class, 'searchDevices'])->name('direct_sale.devices');
            Route::post('/checkout', [DirectSaleController::class, 'checkout'])->name('direct_sale.checkout');
        });

        //blog routes
        Route::prefix('blogs')->group(function () {
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
