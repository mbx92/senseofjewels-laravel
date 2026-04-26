<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DiscountController as AdminDiscountController;
use App\Http\Controllers\Admin\InventoryController as AdminInventoryController;
use App\Http\Controllers\Admin\MediaController as AdminMediaController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PortfolioController as AdminPortfolioController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\SectionController as AdminSectionController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\TestimonialController as AdminTestimonialController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\VoucherController as AdminVoucherController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Customer preference routes (no auth required — session-only)
Route::post('/preferences/locale', [PreferenceController::class, 'setLocale'])->name('preferences.locale');
Route::post('/preferences/currency', [PreferenceController::class, 'setCurrency'])->name('preferences.currency');

// Shop — category route before {slug} to avoid conflict
Route::get('/shop/category/{slug}', [ShopController::class, 'index'])->name('shop.category');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{slug}', [ShopController::class, 'show'])->name('shop.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/items', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/items/{cartItem}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/items/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');

Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::post('/checkout/apply-voucher', [CheckoutController::class, 'applyVoucher'])->name('checkout.apply-voucher');
});

// Payment — webhook excluded from CSRF via bootstrap/app.php
Route::post('/payment/notification', [PaymentController::class, 'notification'])->name('payment.notification');
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/pending', [PaymentController::class, 'pending'])->name('payment.pending');
Route::get('/payment/failed', [PaymentController::class, 'failed'])->name('payment.failed');
Route::post('/payment/token', [PaymentController::class, 'token'])->name('payment.token');
Route::get('/payment/{orderNumber}', [PaymentController::class, 'show'])->name('payment.show');

Route::middleware('auth')->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{orderNumber}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{orderNumber}/invoice', [InvoiceController::class, 'download'])->name('orders.invoice');

    // Account area
    Route::get('/account/tracking', function () {
        $orders = \App\Models\Order::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->take(20)
            ->get();
        return view('account.tracking', compact('orders'));
    })->name('account.tracking');

    Route::get('/account/reviews', function () {
        return view('account.reviews');
    })->name('account.reviews');
});

Route::match(['get', 'post'], '/orders/{orderNumber}/tracking', [OrderTrackingController::class, 'show'])
    ->name('orders.track');

Route::get('/dashboard', function () {
    return redirect()->route('orders.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('guest')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware(['auth', 'role:super-admin|admin|editor'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Landing Page CMS
    Route::get('/hero', [AdminSectionController::class, 'hero'])->name('hero');
    Route::put('/hero', [AdminSectionController::class, 'updateHero'])->name('hero.update');

    Route::get('/about', [AdminSectionController::class, 'about'])->name('about');
    Route::put('/about', [AdminSectionController::class, 'updateAbout'])->name('about.update');

    Route::get('/story', [AdminSectionController::class, 'story'])->name('story');
    Route::put('/story', [AdminSectionController::class, 'updateStory'])->name('story.update');

    Route::get('/contact-settings', [AdminSettingController::class, 'contact'])->name('contact-settings');
    Route::put('/contact-settings', [AdminSettingController::class, 'updateContact'])->name('contact-settings.update');

    // CMS Resources
    Route::resource('services', AdminServiceController::class);
    Route::resource('portfolio', AdminPortfolioController::class);
    Route::resource('testimonials', AdminTestimonialController::class);

    // Shop — Products
    Route::delete('products/{product}/images/{image}', [AdminProductController::class, 'destroyImage'])->name('products.images.destroy');
    Route::resource('products', AdminProductController::class);

    // Shop — Categories
    Route::resource('categories', AdminCategoryController::class);

    // Shop — Inventory
    Route::get('inventory', [AdminInventoryController::class, 'index'])->name('inventory.index');
    Route::post('inventory/adjust', [AdminInventoryController::class, 'adjust'])->name('inventory.adjust');

    // Shop — Discounts & Vouchers
    Route::resource('discounts', AdminDiscountController::class);
    Route::resource('vouchers', AdminVoucherController::class);

    // Shop — Orders
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::get('orders/{orderNumber}/invoice', [InvoiceController::class, 'download'])->name('orders.invoice');

    // System — Users & Roles (super-admin only)
    Route::middleware('role:super-admin')->group(function () {
        Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        Route::get('roles', [AdminRoleController::class, 'index'])->name('roles.index');
        Route::post('roles', [AdminRoleController::class, 'store'])->name('roles.store');
        Route::put('roles/{role}', [AdminRoleController::class, 'update'])->name('roles.update');
        Route::delete('roles/{role}', [AdminRoleController::class, 'destroy'])->name('roles.destroy');
    });

    // System — Settings
    Route::get('settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [AdminSettingController::class, 'update'])->name('settings.update');
    Route::get('integrations', [AdminSettingController::class, 'integrations'])->name('integrations.index');
    Route::put('integrations', [AdminSettingController::class, 'updateIntegrations'])->name('integrations.update');

    // System — Media Library
    Route::get('media/json', [AdminMediaController::class, 'json'])->name('media.json');
    Route::get('media', [AdminMediaController::class, 'index'])->name('media.index');
    Route::post('media', [AdminMediaController::class, 'store'])->name('media.store');
    Route::put('media/{medium}', [AdminMediaController::class, 'update'])->name('media.update');
    Route::delete('media/{medium}', [AdminMediaController::class, 'destroy'])->name('media.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
