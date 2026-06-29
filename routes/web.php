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
    if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->hasRole('seller')) {
        return redirect()->route('seller.dashboard');
    } elseif ($user->hasRole('buyer')) {
        return redirect()->route('buyer.products.index');
    } elseif ($user->hasRole('logistics')) {
        return redirect()->route('logistics.dashboard');
    }
    return redirect('/');
})->name('dashboard')->middleware(['auth', 'legal_acceptance']);

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

    Route::get('/legal-acceptance', [\App\Http\Controllers\LegalAcceptanceController::class, 'show'])->name('legal-acceptance.show');
    Route::post('/legal-acceptance', [\App\Http\Controllers\LegalAcceptanceController::class, 'store'])->name('legal-acceptance.store');
});

Route::get('/products', [\App\Http\Controllers\Buyer\ProductController::class, 'index'])->name('buyer.products.index');
Route::get('/products/search-catalog', function () {
    return \App\Models\Product::select('id', 'name', 'brand_name', 'unit_price', 'image_path', 'origin_lga', 'moq')->take(40)->get();
})->name('products.search-catalog');
Route::get('/products/{id}', [\App\Http\Controllers\Buyer\ProductController::class, 'show'])->name('buyer.products.show');

Route::get('/cart', function () {
    return view('buyer.products.cart');
})->name('buyer.cart.index');

Route::post('/checkout', function (\Illuminate\Http\Request $request) {
    if (!auth()->check()) {
        return response()->json(['status' => 'ok', 'message' => 'Order saved locally']);
    }

    $data = $request->validate([
        'items' => 'required|array',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
        'total' => 'required|numeric|min:0',
    ]);

    $user = auth()->user();
    $buyer = \App\Models\BuyerProfile::firstOrCreate(
        ['user_id' => $user->id],
        ['country' => 'Nigeria', 'verification_status' => 'approved']
    );

    $orderIds = [];
    foreach ($data['items'] as $item) {
        $product = \App\Models\Product::find($item['product_id']);
        if (!$product || !$product->seller_profile_id) continue;

        $orderNumber = 'ORD-' . strtoupper(\Illuminate\Support\Str::random(8));
        $totalAmount = (float) $product->unit_price * $item['quantity'];

        $order = \App\Models\Order::create([
            'order_number' => $orderNumber,
            'product_id' => $product->id,
            'buyer_profile_id' => $buyer->id,
            'seller_profile_id' => $product->seller_profile_id,
            'order_quantity' => $item['quantity'],
            'total_amount' => $totalAmount,
            'currency' => 'NGN',
            'fulfillment_mode' => $product->fulfillment_mode ?? 'seller_direct',
            'fulfillment_status' => 'pending',
            'commission_amount' => round($totalAmount * 0.10, 2),
            'tax_amount' => round($totalAmount * 0.075, 2),
            'net_payout_amount' => round($totalAmount * 0.825, 2),
            'settlement_status' => 'pending',
            'payment_status' => 'held',
            'shipment_status' => 'pending_assignment',
            'status' => 'confirmed',
        ]);

        $orderIds[] = $order->id;
    }

    return response()->json(['status' => 'ok', 'order_ids' => $orderIds]);
})->name('checkout.store');

Route::get('/orders', function () {
    return view('buyer.orders.index');
})->name('buyer.orders.index');

Route::get('/api/world/states/{countryCode}', function (string $countryCode) {
    $states = \Imujas9\World\Facades\State::where('country_code', strtoupper($countryCode))->get(['name', 'code']);
    return response()->json($states);
})->name('api.world.states');

Route::middleware(['auth', 'verified', 'security_policy', 'legal_acceptance'])->group(function () {
    Route::get('/kyc/onboarding', [\App\Http\Controllers\Auth\KycOnboardingController::class, 'show'])->name('kyc.onboarding');
    Route::post('/kyc/onboarding', [\App\Http\Controllers\Auth\KycOnboardingController::class, 'store'])->name('kyc.onboarding.store');
    Route::get('/exporter/onboarding', [\App\Http\Controllers\Auth\KycOnboardingController::class, 'show'])->name('exporter.onboarding');
});

Route::middleware(['auth', 'verified', 'security_policy', 'legal_acceptance'])->group(function () {
    Route::prefix('buyer')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'buyerDashboard'])->name('buyer.dashboard');
        
        // Profile Settings
        Route::get('/profile', [\App\Http\Controllers\Buyer\ProfileController::class, 'show'])->name('buyer.profile.show');
        Route::put('/profile/info', [\App\Http\Controllers\Buyer\ProfileController::class, 'updateInfo'])->name('buyer.profile.update-info');
        Route::put('/profile/password', [\App\Http\Controllers\Buyer\ProfileController::class, 'updatePassword'])->name('buyer.profile.password');
        Route::get('/profile/2fa', [\App\Http\Controllers\Buyer\ProfileController::class, 'showTwoFactor'])->name('buyer.profile.2fa');
        Route::post('/profile/2fa/confirm', [\App\Http\Controllers\Buyer\ProfileController::class, 'confirmTwoFactor'])->name('buyer.profile.2fa.confirm');
        Route::post('/profile/2fa/disable', [\App\Http\Controllers\Buyer\ProfileController::class, 'disableTwoFactor'])->name('buyer.profile.2fa.disable');

        // All Categories
        Route::get('/categories', [\App\Http\Controllers\Buyer\ProductController::class, 'categories'])->name('buyer.categories.index');

        // Order Tracking
        Route::get('/orders/{reference}/track', [\App\Http\Controllers\Buyer\OrderController::class, 'track'])->name('buyer.orders.track');

        // Order Reviews
        Route::get('/orders/{reference}/review', [\App\Http\Controllers\Buyer\OrderController::class, 'review'])->name('buyer.orders.review');
        Route::post('/orders/{reference}/review', [\App\Http\Controllers\Buyer\OrderController::class, 'storeReview'])->name('buyer.orders.review.store');

        // Reorder
        Route::get('/orders/{reference}/reorder', [\App\Http\Controllers\Buyer\OrderController::class, 'reorder'])->name('buyer.orders.reorder');

        // Seller Onboarding for Buyers
        Route::get('/become-a-seller', [\App\Http\Controllers\SellerOnboardingController::class, 'show'])->name('seller.onboarding');
        Route::post('/become-a-seller', [\App\Http\Controllers\SellerOnboardingController::class, 'store'])->name('seller.onboarding.store');
    });
});

Route::middleware(['auth', 'verified', 'security_policy', 'kyc_completed', 'legal_acceptance'])->group(function () {
    Route::prefix('seller')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Seller\DashboardController::class, 'index'])->name('seller.dashboard');
        Route::get('/compliance', [\App\Http\Controllers\Seller\ComplianceController::class, 'index'])->name('seller.compliance.index');
        
        Route::get('/catalog', [\App\Http\Controllers\Seller\ProductController::class, 'index'])->name('seller.catalog.index');
        Route::post('/catalog', [\App\Http\Controllers\Seller\ProductController::class, 'store'])->name('seller.catalog.store');

        Route::get('/inventory', [\App\Http\Controllers\Seller\InventoryController::class, 'index'])->name('seller.inventory.index');
        Route::get('/orders', [\App\Http\Controllers\Seller\OrderController::class, 'index'])->name('seller.orders.index');

        Route::get('/documents', [\App\Http\Controllers\Seller\DocumentController::class, 'index'])->name('seller.documents.index');
        Route::post('/documents', [\App\Http\Controllers\Seller\DocumentController::class, 'store'])->name('seller.documents.store');

        Route::get('/profile', [\App\Http\Controllers\Seller\ProfileController::class, 'show'])->name('seller.profile.show');
        Route::post('/profile', [\App\Http\Controllers\Seller\ProfileController::class, 'update'])->name('seller.profile.update');
    });

    Route::prefix('logistics')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'logisticsDashboard'])->name('logistics.dashboard');
    });

    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'adminDashboard'])->name('admin.dashboard');

    Route::get('/atex/profile', [\App\Http\Controllers\Seller\ProfileController::class, 'show'])->name('admin.profile.show');
    Route::post('/atex/profile', [\App\Http\Controllers\Seller\ProfileController::class, 'update'])->name('admin.profile.update');

    Route::get('/products', [\App\Http\Controllers\Seller\ProductController::class, 'index'])->name('admin.products.index');
    Route::post('/products', [\App\Http\Controllers\Seller\ProductController::class, 'store'])->name('admin.products.store');
    Route::post('/products/{id}/review', [\App\Http\Controllers\Seller\ProductController::class, 'review'])->name('admin.products.review');

    Route::get('/documents', [\App\Http\Controllers\Seller\DocumentController::class, 'index'])->name('admin.documents.index');
    Route::post('/documents', [\App\Http\Controllers\Seller\DocumentController::class, 'store'])->name('admin.documents.store');
    Route::post('/documents/{id}/review', [\App\Http\Controllers\Seller\DocumentController::class, 'review'])->name('admin.documents.review');

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
        Route::get('sellers/trashed', [\App\Http\Controllers\Admin\SellerController::class, 'trashed'])->name('admin.sellers.trashed');
    Route::post('sellers/{id}/restore', [\App\Http\Controllers\Admin\SellerController::class, 'restore'])->name('admin.sellers.restore');
    Route::delete('sellers/{id}/force-delete', [\App\Http\Controllers\Admin\SellerController::class, 'forceDelete'])->name('admin.sellers.force-delete');
    Route::post('sellers/{id}/toggle-status', [\App\Http\Controllers\Admin\SellerController::class, 'toggleStatus'])->name('admin.sellers.toggle-status');
    Route::post('sellers/{id}/resend-welcome', [\App\Http\Controllers\Admin\SellerController::class, 'resendWelcome'])->name('admin.sellers.resend-welcome');
    Route::post('sellers/{id}/resend-verification', [\App\Http\Controllers\Admin\SellerController::class, 'resendVerificationAdmin'])->name('admin.sellers.resend-verification');
    Route::post('sellers/{id}/reset-2fa', [\App\Http\Controllers\Admin\SellerController::class, 'resetTwoFactor'])->name('admin.sellers.reset-2fa');
    Route::post('sellers/{id}/verify-email', [\App\Http\Controllers\Admin\SellerController::class, 'verifyEmail'])->name('admin.sellers.verify-email');
    Route::post('sellers/{id}/reset-password', [\App\Http\Controllers\Admin\SellerController::class, 'resetPassword'])->name('admin.sellers.reset-password');
    Route::get('sellers/{id}/auth-logs', [\App\Http\Controllers\Admin\SellerController::class, 'authLogs'])->name('admin.sellers.auth-logs');
    Route::get('sellers/all-auth-logs', [\App\Http\Controllers\Admin\SellerController::class, 'allAuthLogs'])->name('admin.sellers.all-auth-logs');
    Route::post('sellers/{id}/send-email', [\App\Http\Controllers\Admin\SellerController::class, 'sendCustomEmail'])->name('admin.sellers.send-custom-email');
    Route::post('sellers/bulk-action', [\App\Http\Controllers\Admin\SellerController::class, 'bulkAction'])->name('admin.sellers.bulk-action');
    Route::post('sellers/{id}/unlock', [\App\Http\Controllers\Admin\SellerController::class, 'unlock'])->name('admin.sellers.unlock');
    Route::resource('sellers', \App\Http\Controllers\Admin\SellerController::class)->names('admin.sellers')->except(['create', 'store']);
    Route::controller(\App\Http\Controllers\Admin\BuyerController::class)
        ->prefix('buyers')
        ->name('admin.buyers.')
        ->group(function () {
            Route::get('trashed', 'trashed')->name('trashed');
            Route::get('all-auth-logs', 'allAuthLogs')->name('all-auth-logs');
            Route::post('bulk-action', 'bulkAction')->name('bulk-action');
            
            Route::post('{id}/restore', 'restore')->name('restore');
            Route::delete('{id}/force-delete', 'forceDelete')->name('force-delete');
            Route::post('{id}/toggle-status', 'toggleStatus')->name('toggle-status');
            Route::post('{id}/resend-welcome', 'resendWelcome')->name('resend-welcome');
            Route::post('{id}/resend-verification', 'resendVerificationAdmin')->name('resend-verification');
            Route::post('{id}/reset-2fa', 'resetTwoFactor')->name('reset-2fa');
            Route::post('{id}/verify-email', 'verifyEmail')->name('verify-email');
            Route::post('{id}/reset-password', 'resetPassword')->name('reset-password');
            Route::get('{id}/auth-logs', 'authLogs')->name('auth-logs');
            Route::post('{id}/send-email', 'sendCustomEmail')->name('send-custom-email');
            Route::post('{id}/unlock', 'unlock')->name('unlock');
            Route::post('{id}/become-seller', 'becomeSeller')->name('become-seller');
        });
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
    
    // Legal Documents Management
    Route::middleware('permission:view legal documents|manage legal documents')->group(function () {
        Route::get('legal-documents', [\App\Http\Controllers\Admin\LegalDocumentController::class, 'index'])->name('admin.legal-documents.index');
    });

    Route::middleware('permission:manage legal documents')->group(function () {
        Route::resource('legal-documents', \App\Http\Controllers\Admin\LegalDocumentController::class)->names('admin.legal-documents')->except(['index', 'show']);
        Route::post('legal-documents/{legal_document}/versions', [\App\Http\Controllers\Admin\LegalDocumentController::class, 'storeVersion'])->name('admin.legal-documents.versions.store');
        Route::post('legal-documents/{legal_document}/versions/{version}/activate', [\App\Http\Controllers\Admin\LegalDocumentController::class, 'activateVersion'])->name('admin.legal-documents.versions.activate');
        Route::delete('legal-documents/{legal_document}/versions/{version}', [\App\Http\Controllers\Admin\LegalDocumentController::class, 'destroyVersion'])->name('admin.legal-documents.versions.destroy');
    });

    Route::middleware('permission:view legal documents|manage legal documents')->group(function () {
        Route::get('legal-documents/{legal_document}', [\App\Http\Controllers\Admin\LegalDocumentController::class, 'show'])->name('admin.legal-documents.show');
    });
    
    // Settings (Super Admin Only)
    Route::middleware(['role:super-admin'])->group(function () {
        Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('admin.settings.index');
        Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('admin.settings.update');
        Route::delete('settings/logo', [\App\Http\Controllers\Admin\SettingController::class, 'deleteLogo'])->name('admin.settings.delete-logo');
        Route::post('settings/test-mail', [\App\Http\Controllers\Admin\SettingController::class, 'sendTestMail'])->name('admin.settings.test-mail');
    });

    // System Options Management
    Route::get('business-categories', [\App\Http\Controllers\Admin\BusinessCategoryController::class, 'index'])->name('admin.business-categories.index');
    Route::post('business-categories', [\App\Http\Controllers\Admin\BusinessCategoryController::class, 'store'])->name('admin.business-categories.store');
    Route::delete('business-categories/{id}', [\App\Http\Controllers\Admin\BusinessCategoryController::class, 'destroy'])->name('admin.business-categories.destroy');
    Route::post('business-categories/{id}/toggle-status', [\App\Http\Controllers\Admin\BusinessCategoryController::class, 'toggleStatus'])->name('admin.business-categories.toggle-status');

    Route::get('categories', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('categories', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('admin.categories.store');
    Route::delete('categories/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('admin.categories.destroy');
    Route::post('categories/{id}/toggle-status', [\App\Http\Controllers\Admin\CategoryController::class, 'toggleStatus'])->name('admin.categories.toggle-status');

    Route::get('product-packaging', [\App\Http\Controllers\Admin\ProductPackagingController::class, 'index'])->name('admin.product-packaging.index');
    Route::post('product-packaging', [\App\Http\Controllers\Admin\ProductPackagingController::class, 'store'])->name('admin.product-packaging.store');
    Route::delete('product-packaging/{id}', [\App\Http\Controllers\Admin\ProductPackagingController::class, 'destroy'])->name('admin.product-packaging.destroy');
    Route::post('product-packaging/{id}/toggle-status', [\App\Http\Controllers\Admin\ProductPackagingController::class, 'toggleStatus'])->name('admin.product-packaging.toggle-status');

    Route::get('units', [\App\Http\Controllers\Admin\UnitOfMeasurementController::class, 'index'])->name('admin.units.index');
    Route::post('units', [\App\Http\Controllers\Admin\UnitOfMeasurementController::class, 'store'])->name('admin.units.store');
    Route::delete('units/{id}', [\App\Http\Controllers\Admin\UnitOfMeasurementController::class, 'destroy'])->name('admin.units.destroy');
    Route::post('units/{id}/toggle-status', [\App\Http\Controllers\Admin\UnitOfMeasurementController::class, 'toggleStatus'])->name('admin.units.toggle-status');
    });
});

Route::get('/shop/{unique_id}', [PublicShopController::class, 'show'])->name('public.shop.show');

// ─── Wishlist ───
Route::middleware('auth')->group(function () {
    Route::get('/wishlist', function () {
        return response()->json(\App\Models\Wishlist::where('user_id', auth()->id())->pluck('product_id'));
    })->name('wishlist.list');
    Route::post('/wishlist/toggle', function (\Illuminate\Http\Request $request) {
        $request->validate(['product_id' => 'required|exists:products,id']);
        $existing = \App\Models\Wishlist::where('user_id', auth()->id())->where('product_id', $request->product_id)->first();
        if ($existing) {
            $existing->delete();
            return response()->json(['saved' => false]);
        }
        \App\Models\Wishlist::create(['user_id' => auth()->id(), 'product_id' => $request->product_id]);
        return response()->json(['saved' => true]);
    })->name('wishlist.toggle');
});



