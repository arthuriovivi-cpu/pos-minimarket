<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\WorkerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Kasir\PosController;
use App\Http\Controllers\ReceiptController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Struk: admin boleh semua, kasir hanya miliknya (dicek di controller)
Route::get('/struk/{transaction}', [ReceiptController::class, 'show'])
    ->middleware('auth')
    ->name('receipt.show');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard', [
            'totalProducts' => Product::count(),
            'totalCategories' => Category::count(),
        ]);
    })->name('dashboard');

    Route::resource('categories', CategoryController::class)->except(['create', 'show']);
    Route::resource('products', ProductController::class)->except(['show']);
    Route::resource('workers', WorkerController::class)->only(['index', 'store', 'destroy']);
});

Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/', [PosController::class, 'index'])->name('index');
    Route::post('/checkout', [PosController::class, 'checkout'])->name('checkout');
});