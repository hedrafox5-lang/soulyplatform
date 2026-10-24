<?php

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

// 1-Click Demo Login Route (Available for evaluation)
Route::post('/login/demo/{role}', [AuthController::class, 'quickLogin'])->name('login.demo');

// Native Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Client Portal Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

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
});
