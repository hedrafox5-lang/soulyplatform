<?php

use App\Http\Controllers\Admin\AdminAiCopilotController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\AdminCannedResponseController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminErpStoreController;
use App\Http\Controllers\Admin\AdminHardwareController;
use App\Http\Controllers\Admin\AdminHostOpsController;
use App\Http\Controllers\Admin\AdminInvoiceController;
use App\Http\Controllers\Admin\AdminLeadController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminPromoCodeController;
use App\Http\Controllers\Admin\AdminServiceController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminTicketController;
use App\Http\Controllers\Admin\AdminWalletController;
use App\Http\Controllers\AiAgentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientDeviceController;
use App\Http\Controllers\ClientInvoiceController;
use App\Http\Controllers\ClientPortalController;
use App\Http\Controllers\DeployController;
use App\Http\Controllers\ErpStoreController;
use App\Http\Controllers\ExpressBookingController;
use App\Http\Controllers\HardwareStoreController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web-Based Artisan & Operations Engine (Secured For Restricted Hosting)
|--------------------------------------------------------------------------
*/

// Safe Web-Based Deployment Endpoint (Protected by Secret Token or Auth)
Route::match(['get', 'post'], '/run-souly-deploy-now', function (\Illuminate\Http\Request $request) {
    $secret = config('app.deploy_secret', env('DEPLOY_SECRET'));
    if ($secret && $request->query('token') !== $secret && !auth()->check()) {
        abort(403, 'غير مصرح بتنفيذ عمليات النشر دون تصريح أمني.');
    }
    return app(DeployController::class)->run($request);
})->name('deploy.run');

Route::match(['get', 'post'], '/deploy', function (\Illuminate\Http\Request $request) {
    return redirect()->route('deploy.run', $request->all());
});

// Web-Based Artisan Runner for Migrations & Optimizations (Protected)
Route::match(['get', 'post'], '/run-souly-artisan', function (\Illuminate\Http\Request $request) {
    $secret = config('app.deploy_secret', env('DEPLOY_SECRET'));
    $hasValidToken = $secret && $request->query('token') === $secret;
    $isAuthorizedStaff = auth()->check() && (auth()->user()->is_admin || auth()->user()->role === 'admin' || auth()->user()->role === 'staff');

    if (!$hasValidToken && !$isAuthorizedStaff) {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'يرجى تسجيل الدخول أولاً كمسؤول للوصول إلى أدوات النظام.');
        }
        abort(403, 'غير مصرح لك بتشغيل أوامر النظام الحساسة.');
    }

    $outputs = [];
    $startTime = microtime(true);

    $runCommand = function ($cmd, $params = []) use (&$outputs) {
        try {
            $exitCode = Artisan::call($cmd, $params);
            $rawOutput = trim(Artisan::output());
            $outputs[] = [
                'command' => $cmd . ($params ? ' ' . json_encode($params) : ''),
                'status'  => $exitCode === 0 ? 'SUCCESS' : 'FAILED',
                'output'  => $rawOutput ?: ($exitCode === 0 ? 'Executed successfully (no output)' : 'Failed with exit code ' . $exitCode),
            ];
        } catch (\Throwable $e) {
            $outputs[] = [
                'command' => $cmd,
                'status'  => 'ERROR',
                'output'  => $e->getMessage(),
            ];
        }
    };

    // 1. Run Database Migrations
    $runCommand('migrate', ['--force' => true]);

    // 2. Symlink Public Storage
    $runCommand('storage:link');

    // 3. Clear & Reset Application Caches
    $runCommand('optimize:clear');
    $runCommand('config:clear');
    $runCommand('cache:clear');
    $runCommand('route:clear');
    $runCommand('view:clear');

    $executionTime = round((microtime(true) - $startTime) * 1000, 2);

    $html = '<!DOCTYPE html><html lang="ar" dir="rtl"><head><meta charset="utf-8">';
    $html .= '<title>سولي · تشغيل أوامر النظام</title>';
    $html .= '<style>';
    $html .= 'body { background: #05050A; color: #F8FAFC; font-family: monospace, Tahoma; padding: 30px; margin: 0; }';
    $html .= '.hud-card { max-width: 900px; margin: 0 auto; background: #0B0C16; border: 1px solid #00F5D4; border-radius: 20px; padding: 25px; box-shadow: 0 0 30px rgba(0, 245, 212, 0.2); }';
    $html .= '.title { color: #00F5D4; font-size: 20px; font-weight: bold; margin-bottom: 20px; border-bottom: 1px solid #1C1F36; padding-bottom: 12px; display: flex; justify-content: space-between; align-items: center; }';
    $html .= '.cmd-block { background: #121422; border: 1px solid #1C1F36; border-radius: 12px; margin-bottom: 14px; overflow: hidden; }';
    $html .= '.cmd-header { padding: 10px 14px; background: #16192E; display: flex; justify-content: space-between; font-weight: bold; font-size: 13px; }';
    $html .= '.cmd-out { padding: 12px 14px; font-size: 12px; color: #94A3B8; white-space: pre-wrap; line-height: 1.6; }';
    $html .= '.badge-success { color: #10B981; }';
    $html .= '.badge-error { color: #FF2A55; }';
    $html .= '.btn-return { display: inline-block; margin-top: 15px; padding: 10px 20px; background: #00F5D4; color: #05050A; text-decoration: none; font-weight: bold; border-radius: 10px; font-family: Tahoma; font-size: 13px; }';
    $html .= '</style></head><body>';
    $html .= '<div class="hud-card">';
    $html .= '<div class="title"><span>⚡ تشغيل أوامر Laravel Artisan السحابية</span><span style="font-size:12px;color:#FFB800;">' . $executionTime . ' ms</span></div>';

    foreach ($outputs as $item) {
        $badgeClass = $item['status'] === 'SUCCESS' ? 'badge-success' : 'badge-error';
        $html .= '<div class="cmd-block">';
        $html .= '<div class="cmd-header"><span style="color:#00F5D4;">$ php artisan ' . htmlspecialchars($item['command']) . '</span><span class="' . $badgeClass . '">' . $item['status'] . '</span></div>';
        $html .= '<div class="cmd-out">' . htmlspecialchars($item['output']) . '</div>';
        $html .= '</div>';
    }

    $html .= '<a href="/portal/devices" class="btn-return">الذهاب إلى قسم أجهزتي ←</a>';
    $html .= '</div></body></html>';

    return response($html);
})->name('artisan.run');

/*
|--------------------------------------------------------------------------
| Public Marketing & Showcase Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('services')->name('services.')->group(function () {
    Route::get('/', [ServiceController::class, 'index'])->name('index');
    Route::get('/{service:slug}', [ServiceController::class, 'show'])->name('show');
    Route::get('/{service:slug}/{packageSlug}', [ServiceController::class, 'showPackage'])->name('package');
});

// Standalone BrandERP Modular Marketplace Routes
Route::prefix('store/branderp')->name('store.branderp')->group(function () {
    Route::get('/', [ErpStoreController::class, 'index']);
    Route::post('/wishlist', [ErpStoreController::class, 'toggleWishlist'])->name('.wishlist');
    Route::post('/trial', [ErpStoreController::class, 'startTrial'])->name('.trial');
    Route::post('/checkout', [ErpStoreController::class, 'checkout'])->name('.checkout');
    Route::post('/custom-request', [ErpStoreController::class, 'submitCustomRequest'])->name('.custom_request');
    Route::get('/quotation', [ErpStoreController::class, 'quickQuotation'])->name('.quotation');
});

// Standalone Hardware & POS Equipment Marketplace Routes
Route::prefix('store/hardware')->name('store.hardware')->group(function () {
    Route::get('/', [HardwareStoreController::class, 'index']);
    Route::post('/checkout', [HardwareStoreController::class, 'checkout'])->name('.checkout');
});

// Public Invoice & Warranty Verification (QR Code Direct Scan)
Route::get('/invoice/verify/{hash_id}', [ClientInvoiceController::class, 'verifyPublic'])->name('invoice.verify');

// Souly AI Public API Endpoints
Route::prefix('api/ai')->name('api.ai.')->group(function () {
    Route::post('/chat', [AiAgentController::class, 'chat'])->name('chat');
    Route::get('/history/{sessionUuid?}', [AiAgentController::class, 'history'])->name('history');
});

// Automated Payment Webhook Listeners (Unified Gateway & Paymob)
Route::post('/payment/callback', [PaymentWebhookController::class, 'handleCallback'])->name('payment.callback');
Route::post('/api/paymob/webhook', [PaymentWebhookController::class, 'handleCallback'])->name('api.paymob.webhook');

// Lightweight Cached Catalog Icons API for 0ms IndexedDB Hydration
Route::get('/api/catalog/icons', function () {
    $data = \Illuminate\Support\Facades\Cache::remember('souly_api_catalog_icons_v2', 86400, function () {
        return [
            'status' => 'success',
            'count'  => \App\Models\ServiceIcon::count(),
            'icons'  => \App\Models\ServiceIcon::with('service:id,name,slug')
                ->where('is_active', true)
                ->orderBy('display_order')
                ->get()
                ->map(function ($icon) {
                    return [
                        'id'                 => $icon->id,
                        'service_id'         => $icon->service_id,
                        'service_name'       => $icon->service->name ?? '',
                        'service_slug'       => $icon->service->slug ?? '',
                        'group_type'         => $icon->group_type,
                        'title'              => $icon->title,
                        'icon_name'          => $icon->icon_name,
                        'badge_text'         => $icon->badge_text,
                        'popup_title'        => $icon->popup_title,
                        'popup_summary'      => $icon->popup_summary,
                        'popup_bullets_json' => $icon->popup_bullets_json,
                        'popup_cta_text'     => $icon->popup_cta_text,
                    ];
                })->values()->all(),
        ];
    });

    return response()->json($data);
})->name('api.catalog.icons');

// Universal 1-Tap Express Mobile Booking (Guest & Auth)
Route::post('/express-book', [ExpressBookingController::class, 'store'])->name('express-book.store');

// Protected Quick Login for Local Development Only (Zero Production Exposure)
Route::match(['get', 'post'], '/login/demo/{role}', function ($role) {
    if (!app()->environment('local') || config('app.debug') !== true) {
        abort(403, 'الدخول التجريبي السريع معطل تماماً في بيئة التشغيل لسلامة النظام.');
    }
    return app(AuthController::class)->quickLogin($role);
})->name('login.demo');

// Native Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes (Client Portal, Support & Invoices)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // 1. Client Portal
    Route::prefix('portal')->name('portal.')->group(function () {
        Route::get('/', [ClientPortalController::class, 'index'])->name('index');
        
        // Digital Wallet Operations
        Route::get('/wallet', [ClientPortalController::class, 'wallet'])->name('wallet');
        Route::post('/wallet/deposit', [ClientPortalController::class, 'deposit'])->name('wallet.deposit');

        // Service Bookings (Start & Complete Visits Removed From Client Domain for Security)
        Route::get('/bookings', [ClientPortalController::class, 'bookings'])->name('bookings');
        Route::post('/bookings', [ClientPortalController::class, 'storeBooking'])->name('bookings.store');
        Route::get('/bookings/{booking}/invoice', [ClientPortalController::class, 'invoice'])->name('bookings.invoice');
        Route::get('/bookings/{booking}/certificate', [ClientPortalController::class, 'certificate'])->name('bookings.certificate');

        // Official Invoices, Warranties & RMA Claims
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [ClientInvoiceController::class, 'index'])->name('index');
            Route::get('/{invoice}', [ClientInvoiceController::class, 'show'])->name('show');
            Route::post('/warranty-claim', [ClientInvoiceController::class, 'submitWarrantyClaim'])->name('warranty_claim');
        });

        // Support Tickets
        Route::get('/tickets', [ClientPortalController::class, 'tickets'])->name('tickets');
        Route::post('/tickets', [ClientPortalController::class, 'storeTicket'])->name('tickets.store');
        Route::get('/tickets/{ticket}', [ClientPortalController::class, 'showTicket'])->name('tickets.show');
        Route::post('/tickets/{ticket}/reply', [ClientPortalController::class, 'replyTicket'])->name('tickets.reply');

        // BrandERP Store Hub & License Center
        Route::get('/erp-store', [ClientPortalController::class, 'erpStore'])->name('erp_store');

        // Hardware Orders & Equipment Hub
        Route::get('/hardware', [ClientPortalController::class, 'hardware'])->name('hardware');

        // Client Devices (Dual-Named: portal.devices & portal.devices.index to Guarantee Zero RouteNotFoundException)
        Route::prefix('devices')->name('devices')->group(function () {
            Route::get('/', [ClientDeviceController::class, 'index']); // Responds to route('portal.devices')
            Route::get('/all', [ClientDeviceController::class, 'index'])->name('.index'); // Responds to route('portal.devices.index')
            Route::post('/', [ClientDeviceController::class, 'store'])->name('.store');
            Route::put('/{device}', [ClientDeviceController::class, 'update'])->name('.update');
            Route::delete('/{device}', [ClientDeviceController::class, 'destroy'])->name('.destroy');
            Route::post('/{device}/maintenance', [ClientDeviceController::class, 'requestMaintenance'])->name('.request_maintenance');
        });
    });

    // Unified Notifications Center (Clients, Technicians & Admins)
    Route::get('/notifications', [NotificationController::class, 'list'])->name('notifications.list');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark_all_read');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    /*
    |--------------------------------------------------------------------------
    | Admin & Staff Command Center (Citadel Headquarters)
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->middleware('staff_or_admin')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('index');

        // Manual Notification Broadcast Dispatcher
        Route::post('/notifications/send-manual', [AdminNotificationController::class, 'sendManual'])->name('notifications.send_manual');

        // 1-Tap JSON Database Backup Snapshot (Sovereign Access)
        Route::get('/system/backup-snapshot', [AdminSettingController::class, 'backupSnapshot'])->name('system.backup_snapshot');

        // Admin AI Agent & Copilot System
        Route::prefix('ai-copilot')->name('ai_copilot.')->group(function () {
            Route::get('/', [AdminAiCopilotController::class, 'index'])->name('index');
            Route::post('/chat', [AdminAiCopilotController::class, 'chat'])->name('chat');
            Route::post('/parse-chat', [AdminAiCopilotController::class, 'parseChat'])->name('parse_chat');
            Route::post('/undo/{snapshotId}', [AdminAiCopilotController::class, 'undo'])->name('undo');
            Route::get('/briefing', [AdminAiCopilotController::class, 'briefing'])->name('briefing');
            Route::post('/change-model', [AdminAiCopilotController::class, 'changeModel'])->name('change_model');
            Route::post('/keys/ping/{key}', [AdminAiCopilotController::class, 'pingKey'])->name('keys.ping');
            Route::post('/keys/toggle/{key}', [AdminAiCopilotController::class, 'toggleKey'])->name('keys.toggle');
            Route::post('/keys/store', [AdminAiCopilotController::class, 'storeKey'])->name('keys.store');
            Route::delete('/keys/{key}', [AdminAiCopilotController::class, 'destroyKey'])->name('keys.destroy');
        });

        // Digital Wallets, Approvals & Bonus Top-ups
        Route::get('/wallets', [AdminWalletController::class, 'index'])->name('wallets.index');
        Route::post('/wallets/{transaction}/approve', [AdminWalletController::class, 'approve'])->name('wallets.approve');
        Route::post('/wallets/{transaction}/reject', [AdminWalletController::class, 'reject'])->name('wallets.reject');
        Route::post('/wallets/bonus-gift', [AdminWalletController::class, 'chargeBonusGift'])->name('wallets.bonus_gift');
        Route::post('/wallets/gateways', [AdminWalletController::class, 'updateGateways'])->name('wallets.gateways');

        // Bookings Operations, Field Stopwatch & Invoicing
        Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::post('/bookings/{booking}/update', [AdminBookingController::class, 'update'])->name('bookings.update');
        Route::post('/bookings/{booking}/assign', [AdminBookingController::class, 'assignEngineer'])->name('bookings.assign');
        Route::post('/bookings/{booking}/toggle-call', [AdminBookingController::class, 'toggleCallConfirmed'])->name('bookings.toggle_call');
        Route::post('/bookings/{booking}/stage', [AdminBookingController::class, 'updateStage'])->name('bookings.stage');
        Route::post('/bookings/{booking}/start-visit', [AdminBookingController::class, 'startVisit'])->name('bookings.start_visit');
        Route::post('/bookings/{booking}/complete-visit', [AdminBookingController::class, 'completeVisit'])->name('bookings.complete_visit');
        Route::post('/bookings/{booking}/complete-stopwatch', [AdminBookingController::class, 'completeVisit'])->name('bookings.stopwatch');

        // Invoices, Warranties & Spare Parts Management
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [AdminInvoiceController::class, 'index'])->name('index');
            Route::post('/', [AdminInvoiceController::class, 'store'])->name('store');
            Route::get('/{invoice}', [AdminInvoiceController::class, 'show'])->name('show');
            Route::post('/{invoice}/status', [AdminInvoiceController::class, 'updateStatus'])->name('update_status');
            Route::get('/claims/all', [AdminInvoiceController::class, 'warrantyClaims'])->name('warranty_claims');
            Route::post('/claims/{claim}/resolve', [AdminInvoiceController::class, 'resolveWarrantyClaim'])->name('resolve_warranty');
        });

        // Conditional Promotional Codes & Growth Campaigns
        Route::prefix('promo-codes')->name('promo_codes.')->group(function () {
            Route::get('/', [AdminPromoCodeController::class, 'index'])->name('index');
            Route::post('/', [AdminPromoCodeController::class, 'store'])->name('store');
            Route::post('/{promoCode}/toggle', [AdminPromoCodeController::class, 'toggle'])->name('toggle');
            Route::delete('/{promoCode}', [AdminPromoCodeController::class, 'destroy'])->name('destroy');
        });

        // Mini-CRM Leads Pipeline
        Route::get('/leads', [AdminLeadController::class, 'index'])->name('leads.index');
        Route::post('/leads', [AdminLeadController::class, 'store'])->name('leads.store');
        Route::get('/leads/{lead}', [AdminLeadController::class, 'show'])->name('leads.show');
        Route::post('/leads/{lead}/convert', [AdminLeadController::class, 'convertToUser'])->name('leads.convert');
        Route::post('/leads/{lead}/status', [AdminLeadController::class, 'updateStatus'])->name('leads.status');
        Route::post('/leads/{lead}/activities', [AdminLeadController::class, 'storeActivity'])->name('leads.activity');
        Route::post('/leads/{lead}/toggle-call', [AdminLeadController::class, 'toggleCallConfirmed'])->name('leads.toggle_call');

        // Support Tickets Desk with Isolated Internal Notes
        Route::prefix('tickets')->name('tickets.')->group(function () {
            Route::get('/', [AdminTicketController::class, 'index'])->name('index');
            Route::get('/{ticket}', [AdminTicketController::class, 'show'])->name('show');
            Route::post('/{ticket}/reply', [AdminTicketController::class, 'reply'])->name('reply');
            Route::post('/{ticket}/status', [AdminTicketController::class, 'updateStatus'])->name('status');
            Route::post('/{ticket}/assign', [AdminTicketController::class, 'assign'])->name('assign');
        });

        // Smart Canned Responses Vault
        Route::get('/canned-responses', [AdminCannedResponseController::class, 'index'])->name('canned-responses.index');
        Route::get('/canned-responses/data', [AdminCannedResponseController::class, 'apiList'])->name('canned-responses.data');
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

        // Dynamic System Settings & WhatsApp Template Gear
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/whatsapp-template', [AdminSettingController::class, 'updateWhatsAppTemplate'])->name('settings.update_whatsapp_template');
        Route::post('/settings/sync-pinecone', [AdminSettingController::class, 'syncPinecone'])->name('settings.sync_pinecone');

        // Host Operations Center (Protected Artisan & Log Viewer)
        Route::match(['get', 'post'], '/deploy', [DeployController::class, 'run'])->name('deploy');
        Route::prefix('host-ops')->name('host_ops.')->group(function () {
            Route::get('/', [AdminHostOpsController::class, 'index'])->name('index');
            Route::post('/run', [AdminHostOpsController::class, 'runArtisanCommand'])->name('run');
            Route::get('/logs', [AdminHostOpsController::class, 'viewLogs'])->name('view_logs');
            Route::post('/clear-logs', [AdminHostOpsController::class, 'clearLogs'])->name('clear_logs');
            Route::post('/backup', [AdminHostOpsController::class, 'backupDatabase'])->name('backup');
            Route::get('/backup/download/{fileName}', [AdminHostOpsController::class, 'downloadBackup'])->name('download_backup');
        });

        // BrandERP Store, Orders & Custom Requests Center
        Route::prefix('erp-store')->name('erp_store.')->group(function () {
            Route::get('/', [AdminErpStoreController::class, 'index'])->name('index');
            Route::post('/catalog/{item}/toggle', [AdminErpStoreController::class, 'toggleCatalogItem'])->name('toggle_catalog');
            Route::post('/catalog/{item}/update', [AdminErpStoreController::class, 'updateCatalogItem'])->name('update_catalog');
            Route::post('/licenses/issue-manual', [AdminErpStoreController::class, 'issueManualLicense'])->name('issue_manual_license');
            Route::post('/orders/{order}/status', [AdminErpStoreController::class, 'updateOrderStatus'])->name('order_status');
            Route::post('/custom-requests/{customRequest}/reply', [AdminErpStoreController::class, 'replyCustomRequest'])->name('custom_reply');
            Route::post('/proposals/send', [AdminErpStoreController::class, 'sendProposal'])->name('send_proposal');
        });

        // Hardware Store, Inventory & Order Fulfillment Center
        Route::prefix('hardware')->name('hardware.')->group(function () {
            Route::get('/', [AdminHardwareController::class, 'index'])->name('index');
            Route::post('/products', [AdminHardwareController::class, 'storeProduct'])->name('store_product');
            Route::match(['put', 'post'], '/products/{product}', [AdminHardwareController::class, 'updateProduct'])->name('update_product');
            Route::post('/products/{product}/toggle', [AdminHardwareController::class, 'toggleProduct'])->name('toggle_product');
            Route::post('/products/{product}/inline', [AdminHardwareController::class, 'inlineUpdate'])->name('inline_product');
            Route::delete('/products/{product}', [AdminHardwareController::class, 'deleteProduct'])->name('delete_product');
            Route::post('/orders/{order}/status', [AdminHardwareController::class, 'updateOrderStatus'])->name('order_status');
        });
    });
});
