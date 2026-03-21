<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\DisputeController;
use App\Http\Controllers\KycController;

use App\Http\Controllers\OrderController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\WithdrawalController;

Route::get('/', function () {
    return view('welcome');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {

    // Guest Admin Routes (Login)
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
        Route::post('/forgot-password', [AdminAuthController::class, 'forgotPassword'])->name('forgot-password');
        Route::post('/reset-password', [AdminAuthController::class, 'resetPassword'])->name('reset-password');
    });

    // Authenticated Admin Routes
    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/notifications/count', [DashboardController::class, 'notificationsCount'])->name('notifications.count');
        
        // Support Tickets
        Route::get('/tickets', [SupportTicketController::class, 'index'])->name('tickets.index');
        Route::get('/tickets/{id}', [SupportTicketController::class, 'show'])->name('tickets.show');
        Route::post('/tickets/{id}/status', [SupportTicketController::class, 'updateStatus'])->name('tickets.updateStatus');
        Route::post('/tickets/{id}/priority', [SupportTicketController::class, 'updatePriority'])->name('tickets.updatePriority');
        Route::post('/tickets/{id}/reply', [SupportTicketController::class, 'reply'])->name('tickets.reply');
        
        // Disputes
        Route::get('/disputes', [DisputeController::class, 'index'])->name('disputes.index');
        Route::get('/disputes/{id}', [DisputeController::class, 'show'])->name('disputes.show');
        Route::post('/disputes/{id}/resolve', [DisputeController::class, 'resolve'])->name('disputes.resolve');
        
        // KYC
        Route::get('/kyc', [KycController::class, 'index'])->name('kyc.index');
        Route::get('/kyc/{id}', [KycController::class, 'show'])->name('kyc.show');
        Route::post('/kyc/{id}/approve', [KycController::class, 'approve'])->name('kyc.approve');
        Route::post('/kyc/{id}/reject', [KycController::class, 'reject'])->name('kyc.reject');
        
        // Orders
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
        
        // Merchants
        Route::get('/merchants', [MerchantController::class, 'index'])->name('merchants.index');
        Route::get('/merchants/{id}', [MerchantController::class, 'show'])->name('merchants.show');
        Route::post('/merchants/{id}/approve', [MerchantController::class, 'approve'])->name('merchants.approve');
        Route::post('/merchants/{id}/reject', [MerchantController::class, 'reject'])->name('merchants.reject');
        
        // Withdrawals
        Route::get('/withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::post('/withdrawals/{id}/approve', [WithdrawalController::class, 'approve'])->name('withdrawals.approve');
        Route::post('/withdrawals/{id}/reject', [WithdrawalController::class, 'reject'])->name('withdrawals.reject');
        
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    });
});