<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ShopApprovalController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\PublicShopController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\SecurityEnforcementController;
use App\Http\Controllers\Auth\ForgotPasswordController;

Route::get('/', [\App\Http\Controllers\Atex\LandingPageController::class, 'index'])->name('home');

Route::get('/register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);

Route::get('/dashboard', function () {
    $user = \Illuminate\Support\Facades\Auth::user();
    if ($user->hasRole('super-admin') || $user->hasRole('field-officer')) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->hasRole('exporter')) {
        return redirect()->route('exporter.dashboard');
    } elseif ($user->hasRole('buyer')) {
        return redirect()->route('buyer.dashboard');
    } elseif ($user->hasRole('logistics')) {
        return redirect()->route('logistics.dashboard');
    }
    return redirect('/');
})->name('dashboard');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');

// Public Email Verification
Route::match(['get', 'post'], '/verify-email/{id}/{hash}', [UserController::class, 'verifyEmailLink'])->name('verification.verify');
Route::get('/verify-email/notice', [UserController::class, 'showVerificationNotice'])->name('verification.notice');
Route::post('/verify-email/resend', [UserController::class, 'resendVerification'])->name('verification.resend');
Route::post('/verify-email/bypass', [UserController::class, 'verifyEmailBypass'])->name('verification.bypass');

// Two-Factor Authentication Challenge
Route::get('/2fa-challenge', [LoginController::class, 'showChallenge'])->name('2fa.challenge');
Route::post('/2fa-challenge', [LoginController::class, 'verifyChallenge'])->name('2fa.verify');
Route::post('/2fa-recovery', [LoginController::class, 'verifyRecovery'])->name('2fa.recovery');

// Mandatory Security Enforcement
Route::middleware('auth')->group(function () {
    Route::get('/security/password-change', [SecurityEnforcementController::class, 'showPasswordChange'])->name('security.password');
    Route::post('/security/password-update', [SecurityEnforcementController::class, 'updatePassword'])->name('security.password.update');
    Route::get('/security/2fa-setup', [SecurityEnforcementController::class, 'show2faSetup'])->name('security.2fa');
    Route::post('/security/2fa-confirm', [SecurityEnforcementController::class, 'confirm2fa'])->name('security.2fa.confirm');
});

Route::middleware(['auth', 'verified', 'security_policy'])->group(function () {
    Route::get('/kyc/onboarding', [\App\Http\Controllers\Auth\KycOnboardingController::class, 'show'])->name('kyc.onboarding');
    Route::post('/kyc/onboarding', [\App\Http\Controllers\Auth\KycOnboardingController::class, 'store'])->name('kyc.onboarding.store');
});

Route::middleware(['auth', 'verified', 'security_policy', 'kyc_completed'])->group(function () {
    Route::prefix('exporter')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'exporterDashboard'])->name('exporter.dashboard');
    });

    Route::prefix('buyer')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'buyerDashboard'])->name('buyer.dashboard');
    });

    Route::prefix('logistics')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'logisticsDashboard'])->name('logistics.dashboard');
    });

    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'adminDashboard'])->name('admin.dashboard');

    Route::get('/atex/profile', [\App\Http\Controllers\Exporter\ProfileController::class, 'show'])->name('admin.profile.show');
    Route::post('/atex/profile', [\App\Http\Controllers\Exporter\ProfileController::class, 'update'])->name('admin.profile.update');

    Route::get('/products', [\App\Http\Controllers\Exporter\ProductController::class, 'index'])->name('admin.products.index');
    Route::post('/products', [\App\Http\Controllers\Exporter\ProductController::class, 'store'])->name('admin.products.store');
    Route::post('/products/{id}/review', [\App\Http\Controllers\Exporter\ProductController::class, 'review'])->name('admin.products.review');

    Route::get('/documents', [\App\Http\Controllers\Exporter\DocumentController::class, 'index'])->name('admin.documents.index');
    Route::post('/documents', [\App\Http\Controllers\Exporter\DocumentController::class, 'store'])->name('admin.documents.store');
    Route::post('/documents/{id}/review', [\App\Http\Controllers\Exporter\DocumentController::class, 'review'])->name('admin.documents.review');

    Route::get('/quotes', [\App\Http\Controllers\Buyer\QuoteRequestController::class, 'index'])->name('admin.quotes.index');
    Route::get('/quotes/create', [\App\Http\Controllers\Buyer\QuoteRequestController::class, 'create'])->name('admin.quotes.create');
    Route::post('/quotes', [\App\Http\Controllers\Buyer\QuoteRequestController::class, 'store'])->name('admin.quotes.store');
    Route::get('/quotes/{id}', [\App\Http\Controllers\Buyer\QuoteRequestController::class, 'show'])->name('admin.quotes.show');
    Route::post('/quotes/{id}/respond', [\App\Http\Controllers\Buyer\QuoteRequestController::class, 'respond'])->name('admin.quotes.respond');

    Route::get('/orders', [\App\Http\Controllers\Buyer\OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/create', [\App\Http\Controllers\Buyer\OrderController::class, 'create'])->name('admin.orders.create');
    Route::post('/orders', [\App\Http\Controllers\Buyer\OrderController::class, 'store'])->name('admin.orders.store');
    Route::get('/orders/{id}', [\App\Http\Controllers\Buyer\OrderController::class, 'show'])->name('admin.orders.show');

    Route::get('/inventory', [\App\Http\Controllers\Admin\InventoryController::class, 'index'])->name('admin.inventory.index');
    Route::post('/inventory/receive', [\App\Http\Controllers\Admin\InventoryController::class, 'receive'])->name('admin.inventory.receive');

    Route::get('/fulfillment', [\App\Http\Controllers\Buyer\OrderController::class, 'fulfillment'])->name('admin.fulfillment.index');
    Route::post('/fulfillment/{id}/update', [\App\Http\Controllers\Buyer\OrderController::class, 'fulfillmentUpdate'])->name('admin.fulfillment.update');

    Route::get('/settlements', [\App\Http\Controllers\Admin\SettlementController::class, 'index'])->name('admin.settlements.index');
    Route::post('/settlements/credit', [\App\Http\Controllers\Admin\SettlementController::class, 'credit'])->name('admin.settlements.credit');

    Route::post('/shipment/assign', [\App\Http\Controllers\Logistics\ShipmentController::class, 'assign'])->name('admin.shipment.assign');
    Route::post('/shipment/{id}/update', [\App\Http\Controllers\Logistics\ShipmentController::class, 'update'])->name('admin.shipment.update');

    Route::get('/atex/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.atex.users.index');
    Route::post('/atex/users/status', [\App\Http\Controllers\Admin\UserController::class, 'status'])->name('admin.atex.users.status');

    Route::get('/kyc', [\App\Http\Controllers\Admin\KycController::class, 'index'])->name('admin.kyc.index');
    Route::get('/kyc/{type}/{id}', [\App\Http\Controllers\Admin\KycController::class, 'show'])->name('admin.kyc.show');
    Route::post('/kyc/review', [\App\Http\Controllers\Admin\KycController::class, 'review'])->name('admin.kyc.review');
    Route::post('/kyc/document/{id}/review', [\App\Http\Controllers\Admin\KycController::class, 'reviewDocument'])->name('admin.kyc.document.review');
    Route::post('/kyc/documents/review-all', [\App\Http\Controllers\Admin\KycController::class, 'reviewAllDocuments'])->name('admin.kyc.document.review-all');
    Route::post('/kyc/review-regulatory', [\App\Http\Controllers\Admin\KycController::class, 'reviewRegulatory'])->name('admin.kyc.review-regulatory');

    // Placeholders for missing prototype views
        Route::get('exporters/trashed', [\App\Http\Controllers\Admin\ExporterController::class, 'trashed'])->name('admin.exporters.trashed');
    Route::post('exporters/{id}/restore', [\App\Http\Controllers\Admin\ExporterController::class, 'restore'])->name('admin.exporters.restore');
    Route::delete('exporters/{id}/force-delete', [\App\Http\Controllers\Admin\ExporterController::class, 'forceDelete'])->name('admin.exporters.force-delete');
    Route::post('exporters/{id}/toggle-status', [\App\Http\Controllers\Admin\ExporterController::class, 'toggleStatus'])->name('admin.exporters.toggle-status');
    Route::post('exporters/{id}/resend-welcome', [\App\Http\Controllers\Admin\ExporterController::class, 'resendWelcome'])->name('admin.exporters.resend-welcome');
    Route::post('exporters/{id}/resend-verification', [\App\Http\Controllers\Admin\ExporterController::class, 'resendVerificationAdmin'])->name('admin.exporters.resend-verification');
    Route::post('exporters/{id}/reset-2fa', [\App\Http\Controllers\Admin\ExporterController::class, 'resetTwoFactor'])->name('admin.exporters.reset-2fa');
    Route::post('exporters/{id}/verify-email', [\App\Http\Controllers\Admin\ExporterController::class, 'verifyEmail'])->name('admin.exporters.verify-email');
    Route::post('exporters/{id}/reset-password', [\App\Http\Controllers\Admin\ExporterController::class, 'resetPassword'])->name('admin.exporters.reset-password');
    Route::get('exporters/{id}/auth-logs', [\App\Http\Controllers\Admin\ExporterController::class, 'authLogs'])->name('admin.exporters.auth-logs');
    Route::get('exporters/all-auth-logs', [\App\Http\Controllers\Admin\ExporterController::class, 'allAuthLogs'])->name('admin.exporters.all-auth-logs');
    Route::post('exporters/{id}/send-email', [\App\Http\Controllers\Admin\ExporterController::class, 'sendCustomEmail'])->name('admin.exporters.send-custom-email');
    Route::post('exporters/bulk-action', [\App\Http\Controllers\Admin\ExporterController::class, 'bulkAction'])->name('admin.exporters.bulk-action');
    Route::post('exporters/{id}/unlock', [\App\Http\Controllers\Admin\ExporterController::class, 'unlock'])->name('admin.exporters.unlock');
    Route::resource('exporters', \App\Http\Controllers\Admin\ExporterController::class)->names('admin.exporters')->except(['create', 'store']);
        Route::get('buyers/trashed', [\App\Http\Controllers\Admin\BuyerController::class, 'trashed'])->name('admin.buyers.trashed');
    Route::post('buyers/{id}/restore', [\App\Http\Controllers\Admin\BuyerController::class, 'restore'])->name('admin.buyers.restore');
    Route::delete('buyers/{id}/force-delete', [\App\Http\Controllers\Admin\BuyerController::class, 'forceDelete'])->name('admin.buyers.force-delete');
    Route::post('buyers/{id}/toggle-status', [\App\Http\Controllers\Admin\BuyerController::class, 'toggleStatus'])->name('admin.buyers.toggle-status');
    Route::post('buyers/{id}/resend-welcome', [\App\Http\Controllers\Admin\BuyerController::class, 'resendWelcome'])->name('admin.buyers.resend-welcome');
    Route::post('buyers/{id}/resend-verification', [\App\Http\Controllers\Admin\BuyerController::class, 'resendVerificationAdmin'])->name('admin.buyers.resend-verification');
    Route::post('buyers/{id}/reset-2fa', [\App\Http\Controllers\Admin\BuyerController::class, 'resetTwoFactor'])->name('admin.buyers.reset-2fa');
    Route::post('buyers/{id}/verify-email', [\App\Http\Controllers\Admin\BuyerController::class, 'verifyEmail'])->name('admin.buyers.verify-email');
    Route::post('buyers/{id}/reset-password', [\App\Http\Controllers\Admin\BuyerController::class, 'resetPassword'])->name('admin.buyers.reset-password');
    Route::get('buyers/{id}/auth-logs', [\App\Http\Controllers\Admin\BuyerController::class, 'authLogs'])->name('admin.buyers.auth-logs');
    Route::get('buyers/all-auth-logs', [\App\Http\Controllers\Admin\BuyerController::class, 'allAuthLogs'])->name('admin.buyers.all-auth-logs');
    Route::post('buyers/{id}/send-email', [\App\Http\Controllers\Admin\BuyerController::class, 'sendCustomEmail'])->name('admin.buyers.send-custom-email');
    Route::post('buyers/bulk-action', [\App\Http\Controllers\Admin\BuyerController::class, 'bulkAction'])->name('admin.buyers.bulk-action');
    Route::post('buyers/{id}/unlock', [\App\Http\Controllers\Admin\BuyerController::class, 'unlock'])->name('admin.buyers.unlock');
    Route::resource('buyers', \App\Http\Controllers\Admin\BuyerController::class)->names('admin.buyers')->except(['create', 'store']);
        Route::get('logistics/trashed', [\App\Http\Controllers\Admin\LogisticsController::class, 'trashed'])->name('admin.logistics.trashed');
    Route::post('logistics/{id}/restore', [\App\Http\Controllers\Admin\LogisticsController::class, 'restore'])->name('admin.logistics.restore');
    Route::delete('logistics/{id}/force-delete', [\App\Http\Controllers\Admin\LogisticsController::class, 'forceDelete'])->name('admin.logistics.force-delete');
    Route::post('logistics/{id}/toggle-status', [\App\Http\Controllers\Admin\LogisticsController::class, 'toggleStatus'])->name('admin.logistics.toggle-status');
    Route::post('logistics/{id}/resend-welcome', [\App\Http\Controllers\Admin\LogisticsController::class, 'resendWelcome'])->name('admin.logistics.resend-welcome');
    Route::post('logistics/{id}/resend-verification', [\App\Http\Controllers\Admin\LogisticsController::class, 'resendVerificationAdmin'])->name('admin.logistics.resend-verification');
    Route::post('logistics/{id}/reset-2fa', [\App\Http\Controllers\Admin\LogisticsController::class, 'resetTwoFactor'])->name('admin.logistics.reset-2fa');
    Route::post('logistics/{id}/verify-email', [\App\Http\Controllers\Admin\LogisticsController::class, 'verifyEmail'])->name('admin.logistics.verify-email');
    Route::post('logistics/{id}/reset-password', [\App\Http\Controllers\Admin\LogisticsController::class, 'resetPassword'])->name('admin.logistics.reset-password');
    Route::get('logistics/{id}/auth-logs', [\App\Http\Controllers\Admin\LogisticsController::class, 'authLogs'])->name('admin.logistics.auth-logs');
    Route::get('logistics/all-auth-logs', [\App\Http\Controllers\Admin\LogisticsController::class, 'allAuthLogs'])->name('admin.logistics.all-auth-logs');
    Route::post('logistics/{id}/send-email', [\App\Http\Controllers\Admin\LogisticsController::class, 'sendCustomEmail'])->name('admin.logistics.send-custom-email');
    Route::post('logistics/bulk-action', [\App\Http\Controllers\Admin\LogisticsController::class, 'bulkAction'])->name('admin.logistics.bulk-action');
    Route::post('logistics/{id}/unlock', [\App\Http\Controllers\Admin\LogisticsController::class, 'unlock'])->name('admin.logistics.unlock');
    Route::resource('logistics', \App\Http\Controllers\Admin\LogisticsController::class)->names('admin.logistics')->except(['create', 'store']);
    Route::get('/payouts', function() { return 'Payouts View Placeholder'; })->name('admin.payouts.index');

    Route::get('/audit', [\App\Http\Controllers\Admin\AuditController::class, 'index'])->name('admin.audit.index');

    // Profile Settings
    Route::get('/profile', [ProfileController::class, 'index'])->name('admin.profile');
    Route::put('/profile/info', [ProfileController::class, 'updateInfo'])->name('admin.profile.update-info');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('admin.profile.password');
    Route::get('/profile/2fa', [ProfileController::class, 'showTwoFactor'])->name('admin.profile.2fa');
    Route::post('/profile/2fa/confirm', [ProfileController::class, 'confirmTwoFactor'])->name('admin.profile.2fa.confirm');
    Route::post('/profile/2fa/disable', [ProfileController::class, 'disableTwoFactor'])->name('admin.profile.2fa.disable');

    Route::get('/shops', [ShopApprovalController::class, 'index'])->name('admin.shops.index');
    Route::post('/shops/{id}/approve', [ShopApprovalController::class, 'approve'])->name('admin.shops.approve');
    Route::post('/shops/{id}/reject', [ShopApprovalController::class, 'reject'])->name('admin.shops.reject');

    // User Management
    Route::get('users/trashed', [UserController::class, 'trashed'])->name('admin.users.trashed');
    Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('admin.users.restore');
    Route::delete('users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('admin.users.force-delete');
    Route::post('users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('admin.users.toggle-status');
    Route::post('users/{id}/resend-welcome', [UserController::class, 'resendWelcome'])->name('admin.users.resend-welcome');
    Route::post('users/{id}/resend-verification', [UserController::class, 'resendVerificationAdmin'])->name('admin.users.resend-verification');
    Route::post('users/{id}/reset-2fa', [UserController::class, 'resetTwoFactor'])->name('admin.users.reset-2fa');
    Route::post('users/{id}/verify-email', [UserController::class, 'verifyEmail'])->name('admin.users.verify-email');
    Route::post('users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('admin.users.reset-password');
    Route::get('users/{id}/auth-logs', [UserController::class, 'authLogs'])->name('admin.users.auth-logs');
    Route::get('users/all-auth-logs', [UserController::class, 'allAuthLogs'])->name('admin.users.all-auth-logs');
    Route::post('users/{id}/send-email', [UserController::class, 'sendCustomEmail'])->name('admin.users.send-custom-email');
    Route::post('users/bulk-action', [UserController::class, 'bulkAction'])->name('admin.users.bulk-action');
    Route::post('users/{id}/unlock', [UserController::class, 'unlock'])->name('admin.users.unlock');
    Route::resource('users', UserController::class)->names('admin.users')->middleware('permission:manage users');
    Route::resource('roles', RoleController::class)->names('admin.roles')->middleware('permission:manage roles');
    
    // Settings (Super Admin Only)
    Route::middleware(['role:super-admin'])->group(function () {
        Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('admin.settings.index');
        Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('admin.settings.update');
        Route::delete('settings/logo', [\App\Http\Controllers\Admin\SettingController::class, 'deleteLogo'])->name('admin.settings.delete-logo');
        Route::post('settings/test-mail', [\App\Http\Controllers\Admin\SettingController::class, 'sendTestMail'])->name('admin.settings.test-mail');
    });
    });
});

Route::get('/shop/{unique_id}', [PublicShopController::class, 'show'])->name('public.shop.show');



