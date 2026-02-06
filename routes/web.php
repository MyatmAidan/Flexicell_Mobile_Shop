<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PhoneModelController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;

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

        //device routes
        Route::prefix('device')->group(function () {
            Route::get('/', [DeviceController::class, 'index'])->name('device.index');
            Route::get('/list', [DeviceController::class, 'getList'])->name('device.getList');
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
    });
});
