<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\DisputeController;
use App\Http\Controllers\KycController;

Route::get('/', function () {
    return view('welcome');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {

    // Guest Admin Routes (Login)
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    });

    // Authenticated Admin Routes
    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
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
        
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    });
});