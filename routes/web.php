<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InboundController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RackController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard or login
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/quick-login/{role}', [AuthController::class, 'quickLogin'])->name('quick-login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Shared Dashboard (tailored per role)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Receipt print & Barcode print (accessible to users who process or inspect them)
    Route::get('/pos/receipt/{transaction}', [PosController::class, 'receipt'])->name('pos.receipt');
    Route::get('/products/{tire}/barcode', [ProductController::class, 'barcode'])->name('products.barcode');

    // Modul Kasir (POS)
    Route::middleware('role:kasir,owner')->group(function () {
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::get('/pos/search', [PosController::class, 'searchTires'])->name('pos.search');
        Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
        Route::post('/pos/void/{transaction}', [PosController::class, 'void'])->name('pos.void');
        Route::get('/pos/history', [PosController::class, 'history'])->name('pos.history');

        // Cashier Shifts
        Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts.index');
        Route::post('/shifts/start', [ShiftController::class, 'startShift'])->name('shifts.start');
        Route::post('/shifts/{shift}/close', [ShiftController::class, 'closeShift'])->name('shifts.close');
    });

    // Modul Inventaris & Gudang
    Route::middleware('role:gudang')->group(function () {
        Route::resource('products', ProductController::class);
        Route::post('/products/{tire}/adjust', [ProductController::class, 'adjustStock'])->name('products.adjust');
        Route::resource('inbounds', InboundController::class)->only(['index', 'create', 'store', 'show']);
        Route::resource('suppliers', SupplierController::class);
        Route::resource('racks', RackController::class)->only(['index', 'store', 'update', 'destroy']);
    });

    // Modul Laporan & Analitik (Owner & Admin)
    Route::middleware('role:owner')->group(function () {
        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/stock-card', [ReportController::class, 'stockCard'])->name('reports.stock-card');
        Route::get('/reports/dead-stock', [ReportController::class, 'deadStock'])->name('reports.dead-stock');
        Route::get('/reports/audit-trail', [ReportController::class, 'auditTrail'])->name('reports.audit-trail');
    });

    // Modul Manajemen Sistem (Admin)
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);
        Route::post('/users/{user}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

        Route::get('/system/logs', [SystemController::class, 'logs'])->name('system.logs');
        Route::post('/system/logs/clear', [SystemController::class, 'clearLogs'])->name('system.logs.clear');

        Route::get('/system/backup', [SystemController::class, 'backup'])->name('system.backup');
        Route::get('/system/backup/download', [SystemController::class, 'downloadBackup'])->name('system.backup.download');
    });
});
