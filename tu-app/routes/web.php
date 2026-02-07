<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard - Role-based redirect
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile routes - All authenticated users
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // =========================================================================
    // MASTER DATA ROUTES - Admin Master Data & System Admin
    // =========================================================================
    Route::middleware(['role:admin_master_data|system_admin'])->group(function () {
        Route::resource('student-categories', \App\Http\Controllers\StudentCategoryController::class);
        Route::resource('students', \App\Http\Controllers\StudentController::class);
        Route::resource('fees', \App\Http\Controllers\FeeController::class);
    });

    // =========================================================================
    // TRANSACTION ROUTES - Petugas Transaksi & System Admin
    // =========================================================================
    Route::middleware(['role:petugas_transaksi|system_admin'])->group(function () {
        Route::resource('payments', \App\Http\Controllers\PaymentController::class)->except(['edit', 'update']);
        Route::get('payments/{payment}/print', [\App\Http\Controllers\PaymentController::class, 'printReceipt'])->name('payments.print');
    });

    // =========================================================================
    // VIEW PAYMENTS - Bendahara, Yayasan can view
    // =========================================================================
    Route::middleware(['role:bendahara|yayasan|admin_master_data'])->group(function () {
        Route::get('payments-view', [\App\Http\Controllers\PaymentController::class, 'index'])->name('payments.view');
        Route::get('payments-view/{payment}', [\App\Http\Controllers\PaymentController::class, 'show'])->name('payments.view.show');
    });

    // =========================================================================
    // ACCOUNTING ROUTES - Bendahara & System Admin
    // =========================================================================
    Route::middleware(['role:bendahara|system_admin'])->prefix('accounting')->name('accounting.')->group(function () {
        Route::get('accounts', [\App\Http\Controllers\AccountingController::class, 'accounts'])->name('accounts');
        Route::get('journal', [\App\Http\Controllers\AccountingController::class, 'journal'])->name('journal');
        Route::get('ledger', [\App\Http\Controllers\AccountingController::class, 'ledger'])->name('ledger');
        Route::get('monthly-summary', [\App\Http\Controllers\AccountingController::class, 'monthlySummary'])->name('monthly-summary');
        Route::get('trial-balance', [\App\Http\Controllers\AccountingController::class, 'trialBalance'])->name('trial-balance');
    });

    // =========================================================================
    // GOVERNANCE ROUTES - System Admin only
    // =========================================================================
    Route::middleware(['role:system_admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', \App\Http\Controllers\UserController::class);
        Route::get('backup', [\App\Http\Controllers\BackupController::class, 'index'])->name('backup.index');
        Route::post('backup', [\App\Http\Controllers\BackupController::class, 'create'])->name('backup.create');
        Route::get('backup/{filename}', [\App\Http\Controllers\BackupController::class, 'download'])->name('backup.download');
        Route::get('audit-logs', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit-logs.index');
    });

    // =========================================================================
    // CORRECTION ROUTES
    // =========================================================================
    // Request correction - Petugas Transaksi
    Route::middleware(['role:petugas_transaksi'])->group(function () {
        Route::get('corrections/create/{payment}', [\App\Http\Controllers\PaymentCorrectionController::class, 'create'])->name('corrections.create');
        Route::post('corrections', [\App\Http\Controllers\PaymentCorrectionController::class, 'store'])->name('corrections.store');
    });

    // Approve correction - Bendahara
    Route::middleware(['role:bendahara|system_admin'])->prefix('corrections')->name('corrections.')->group(function () {
        Route::get('/', [\App\Http\Controllers\PaymentCorrectionController::class, 'index'])->name('index');
        Route::get('/{correction}', [\App\Http\Controllers\PaymentCorrectionController::class, 'show'])->name('show');
        Route::post('/{correction}/approve', [\App\Http\Controllers\PaymentCorrectionController::class, 'approve'])->name('approve');
        Route::post('/{correction}/reject', [\App\Http\Controllers\PaymentCorrectionController::class, 'reject'])->name('reject');
    });

    // =========================================================================
    // DASHBOARD ROUTES - Role-specific access
    // =========================================================================
    Route::middleware(['role:bendahara|system_admin'])->group(function () {
        Route::get('dashboard/bendahara', [DashboardController::class, 'bendaharaDashboard'])->name('dashboard.bendahara');
        Route::get('tunggakan', [\App\Http\Controllers\TunggakanController::class, 'index'])->name('tunggakan.index');

        // Report exports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [\App\Http\Controllers\ReportController::class, 'index'])->name('index');
            Route::get('/tunggakan/export', [\App\Http\Controllers\ReportController::class, 'exportTunggakan'])->name('tunggakan');
            Route::get('/rekap-bulanan/export', [\App\Http\Controllers\ReportController::class, 'exportRekapBulanan'])->name('rekap-bulanan');
        });
    });

    Route::middleware(['role:yayasan|system_admin'])->group(function () {
        Route::get('dashboard/yayasan', [DashboardController::class, 'yayasanDashboard'])->name('dashboard.yayasan');
    });
});

require __DIR__ . '/auth.php';
