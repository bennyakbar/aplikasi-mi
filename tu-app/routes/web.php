<?php

use App\Http\Controllers\ProfileController;
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
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Master Data Routes
    Route::resource('student-categories', \App\Http\Controllers\StudentCategoryController::class);
    Route::resource('students', \App\Http\Controllers\StudentController::class);
    Route::resource('fees', \App\Http\Controllers\FeeController::class);

    // Transaction Routes
    Route::resource('payments', \App\Http\Controllers\PaymentController::class)->except(['edit', 'update']);
    Route::get('payments/{payment}/print', [\App\Http\Controllers\PaymentController::class, 'printReceipt'])->name('payments.print');

    // Accounting Routes
    Route::prefix('accounting')->name('accounting.')->group(function () {
        Route::get('accounts', [\App\Http\Controllers\AccountingController::class, 'accounts'])->name('accounts');
        Route::get('journal', [\App\Http\Controllers\AccountingController::class, 'journal'])->name('journal');
        Route::get('ledger', [\App\Http\Controllers\AccountingController::class, 'ledger'])->name('ledger');
        Route::get('monthly-summary', [\App\Http\Controllers\AccountingController::class, 'monthlySummary'])->name('monthly-summary');
        Route::get('trial-balance', [\App\Http\Controllers\AccountingController::class, 'trialBalance'])->name('trial-balance');
    });
});

require __DIR__ . '/auth.php';
