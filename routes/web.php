<?php

use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\AdminCannedResponseController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminLeadController;
use App\Http\Controllers\Admin\AdminServiceController;
use App\Http\Controllers\Admin\AdminWalletController;
use App\Http\Controllers\AiAgentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientPortalController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

// Public Citadel Showcase Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('services')->name('services.')->group(function () {
    Route::get('/', [ServiceController::class, 'index'])->name('index');
    Route::get('/{service:slug}', [ServiceController::class, 'show'])->name('show');
    Route::get('/{service:slug}/{packageSlug}', [ServiceController::class, 'showPackage'])->name('package');
});

// Souly AI Agent Public API Endpoints
Route::prefix('api/ai')->name('api.ai.')->group(function () {
    Route::post('/chat', [AiAgentController::class, 'chat'])->name('chat');
    Route::get('/history/{sessionUuid}', [AiAgentController::class, 'history'])->name('history');
});

// 1-Click Demo Login Route (For testing & evaluation)
Route::post('/login/demo/{role}', [AuthController::class, 'quickLogin'])->name('login.demo');

// Native Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // 1. Client Portal
    Route::prefix('portal')->name('portal.')->group(function () {
        Route::get('/', [ClientPortalController::class, 'index'])->name('index');
        
        // Digital Wallet
        Route::get('/wallet', [ClientPortalController::class, 'wallet'])->name('wallet');
        Route::post('/wallet/deposit', [ClientPortalController::class, 'deposit'])->name('wallet.deposit');

        // Service Bookings
        Route::get('/bookings', [ClientPortalController::class, 'bookings'])->name('bookings');
        Route::post('/bookings', [ClientPortalController::class, 'storeBooking'])->name('bookings.store');

        // Support Tickets
        Route::get('/tickets', [ClientPortalController::class, 'tickets'])->name('tickets');
        Route::post('/tickets', [ClientPortalController::class, 'storeTicket'])->name('tickets.store');
        Route::get('/tickets/{ticket}', [ClientPortalController::class, 'showTicket'])->name('tickets.show');
        Route::post('/tickets/{ticket}/reply', [ClientPortalController::class, 'replyTicket'])->name('tickets.reply');
    });

    // 2. Admin & Staff Command Center
    Route::prefix('admin')->name('admin.')->middleware('staff_or_admin')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('index');

        // Wallet Top-Ups & Ledger
        Route::get('/wallets', [AdminWalletController::class, 'index'])->name('wallets.index');
        Route::post('/wallets/{transaction}/approve', [AdminWalletController::class, 'approve'])->name('wallets.approve');
        Route::post('/wallets/{transaction}/reject', [AdminWalletController::class, 'reject'])->name('wallets.reject');

        // Bookings Operations
        Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::post('/bookings/{booking}/update', [AdminBookingController::class, 'update'])->name('bookings.update');

        // Mini-CRM Leads Pipeline
        Route::get('/leads', [AdminLeadController::class, 'index'])->name('leads.index');
        Route::post('/leads', [AdminLeadController::class, 'store'])->name('leads.store');
        Route::get('/leads/{lead}', [AdminLeadController::class, 'show'])->name('leads.show');
        Route::post('/leads/{lead}/status', [AdminLeadController::class, 'updateStatus'])->name('leads.status');
        Route::post('/leads/{lead}/activities', [AdminLeadController::class, 'storeActivity'])->name('leads.activity');

        // Smart Canned Responses Vault
        Route::get('/canned-responses', [AdminCannedResponseController::class, 'index'])->name('canned-responses.index');
        Route::post('/canned-responses', [AdminCannedResponseController::class, 'store'])->name('canned-responses.store');
        Route::post('/canned-responses/{response}/increment-usage', [AdminCannedResponseController::class, 'incrementUsage'])->name('canned-responses.increment');
        Route::match(['put', 'post'], '/canned-responses/{response}', [AdminCannedResponseController::class, 'update'])->name('canned-responses.update');
        Route::delete('/canned-responses/{response}', [AdminCannedResponseController::class, 'destroy'])->name('canned-responses.destroy');

        // Services & Packages CMS
        Route::get('/services', [AdminServiceController::class, 'index'])->name('services.index');
        Route::post('/services/{service}/packages', [AdminServiceController::class, 'storePackage'])->name('services.packages.store');
        Route::match(['put', 'post'], '/packages/{package}', [AdminServiceController::class, 'updatePackage'])->name('services.packages.update');
        Route::delete('/packages/{package}', [AdminServiceController::class, 'deletePackage'])->name('services.packages.destroy');
        Route::post('/services/{service}/icons', [AdminServiceController::class, 'storeIcon'])->name('services.icons.store');
        Route::delete('/icons/{icon}', [AdminServiceController::class, 'deleteIcon'])->name('services.icons.destroy');
    });
});
