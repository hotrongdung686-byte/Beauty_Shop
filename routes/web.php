<?php

use App\Http\Controllers\Account\AppointmentController as AccountAppointmentController;
use App\Http\Controllers\Account\OrderController as AccountOrderController;
use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ServiceCategoryController as AdminServiceCategoryController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\StaffController as AdminStaffController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentGatewayController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storefront (public)
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/san-pham', [ProductController::class, 'index'])->name('products.index');
Route::get('/san-pham/{product:slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/dich-vu', [ServiceController::class, 'index'])->name('services.index');
Route::get('/dich-vu/{service:slug}', [ServiceController::class, 'show'])->name('services.show');

Route::get('/gio-hang', [CartController::class, 'index'])->name('cart.index');
Route::post('/gio-hang/them', [CartController::class, 'add'])->name('cart.add');
Route::patch('/gio-hang/{variant}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/gio-hang/{variant}', [CartController::class, 'remove'])->name('cart.remove');

/*
|--------------------------------------------------------------------------
| Payment gateway return / callback / webhook (public, no auth — the
| gateway or the customer's browser hits these directly)
|--------------------------------------------------------------------------
*/

Route::prefix('thanh-toan')->name('payment.')->group(function () {
    Route::get('vnpay/return', [PaymentController::class, 'vnpayReturn'])->name('vnpay.return');
    Route::get('vnpay/ipn', [PaymentController::class, 'vnpayIpn'])->name('vnpay.ipn');

    Route::get('momo/return', [PaymentController::class, 'momoReturn'])->name('momo.return');
    Route::post('momo/ipn', [PaymentController::class, 'momoIpn'])->name('momo.ipn');

    Route::get('zalopay/return', [PaymentController::class, 'zalopayReturn'])->name('zalopay.return');
    Route::post('zalopay/callback', [PaymentController::class, 'zalopayCallback'])->name('zalopay.callback');
});

Route::post('/webhooks/sepay', [PaymentController::class, 'sepayWebhook'])->name('payment.sepay.webhook');

/*
|--------------------------------------------------------------------------
| Authenticated customer area
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/thanh-toan', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/thanh-toan', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::post('/thanh-toan/ma-giam-gia', [CheckoutController::class, 'applyCoupon'])->name('checkout.coupon.apply');
    Route::delete('/thanh-toan/ma-giam-gia', [CheckoutController::class, 'removeCoupon'])->name('checkout.coupon.remove');
    Route::get('/thanh-toan/thanh-cong/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

    Route::get('/thanh-toan/cong/{order}', [PaymentGatewayController::class, 'show'])->name('payment.gateway.show');
    Route::post('/thanh-toan/cong/{order}/vnpay', [PaymentGatewayController::class, 'redirectVnpay'])->name('payment.gateway.vnpay');
    Route::post('/thanh-toan/cong/{order}/momo', [PaymentGatewayController::class, 'redirectMomo'])->name('payment.gateway.momo');
    Route::post('/thanh-toan/cong/{order}/zalopay', [PaymentGatewayController::class, 'redirectZalopay'])->name('payment.gateway.zalopay');
    Route::post('/thanh-toan/cong/{order}/gia-lap', [PaymentGatewayController::class, 'simulate'])->name('payment.gateway.simulate');

    Route::get('/dat-lich/{service:slug}', [BookingController::class, 'create'])->name('booking.create');
    Route::get('/dat-lich/{service:slug}/khung-gio', [BookingController::class, 'slots'])->name('booking.slots');
    Route::post('/dat-lich/{service:slug}', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/dat-lich/thanh-cong/{appointment}', [BookingController::class, 'success'])->name('booking.success');

    Route::post('/san-pham/{product}/danh-gia', [ReviewController::class, 'storeProduct'])->name('reviews.product.store');
    Route::post('/dich-vu/{service}/danh-gia', [ReviewController::class, 'storeService'])->name('reviews.service.store');

    Route::post('/yeu-thich/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::get('/tai-khoan/yeu-thich', [WishlistController::class, 'index'])->name('wishlist.index');

    Route::prefix('tai-khoan')->name('account.')->group(function () {
        Route::get('don-hang', [AccountOrderController::class, 'index'])->name('orders.index');
        Route::get('don-hang/{order}', [AccountOrderController::class, 'show'])->name('orders.show');
        Route::post('don-hang/{order}/huy', [AccountOrderController::class, 'cancel'])->name('orders.cancel');

        Route::get('lich-hen', [AccountAppointmentController::class, 'index'])->name('appointments.index');
        Route::get('lich-hen/{appointment}', [AccountAppointmentController::class, 'show'])->name('appointments.show');
        Route::post('lich-hen/{appointment}/huy', [AccountAppointmentController::class, 'cancel'])->name('appointments.cancel');
    });

    Route::get('/dashboard', function () {
        $user = auth()->user();

        return redirect(in_array($user->role, ['manager', 'staff'], true) ? route('admin.dashboard') : route('home'));
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin / Manager area
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:manager,staff'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', AdminProductController::class)->except(['show']);
    Route::post('products/{product}/variants', [AdminProductController::class, 'storeVariant'])->name('products.variants.store');
    Route::patch('products/{product}/variants/{variant}', [AdminProductController::class, 'updateVariant'])->name('products.variants.update');
    Route::delete('products/{product}/variants/{variant}', [AdminProductController::class, 'destroyVariant'])->name('products.variants.destroy');
    Route::post('products/{product}/images', [AdminProductController::class, 'storeImage'])->name('products.images.store');
    Route::delete('products/{product}/images/{image}', [AdminProductController::class, 'destroyImage'])->name('products.images.destroy');
    Route::patch('products/{product}/images/{image}/primary', [AdminProductController::class, 'setPrimaryImage'])->name('products.images.primary');

    Route::resource('banners', AdminBannerController::class)->except(['show']);

    Route::resource('categories', AdminCategoryController::class)->except(['show']);
    Route::resource('brands', AdminBrandController::class)->except(['show']);
    Route::resource('coupons', AdminCouponController::class)->except(['show']);

    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::patch('orders/{order}/shipment', [AdminOrderController::class, 'updateShipment'])->name('orders.shipment');

    Route::resource('services', AdminServiceController::class)->except(['show']);
    Route::resource('service-categories', AdminServiceCategoryController::class)->except(['show']);
    Route::resource('staff', AdminStaffController::class)->except(['show']);

    Route::get('appointments', [AdminAppointmentController::class, 'index'])->name('appointments.index');
    Route::get('appointments/{appointment}', [AdminAppointmentController::class, 'show'])->name('appointments.show');
    Route::patch('appointments/{appointment}/status', [AdminAppointmentController::class, 'updateStatus'])->name('appointments.status');
    Route::patch('appointments/{appointment}/staff', [AdminAppointmentController::class, 'assignStaff'])->name('appointments.assign-staff');
});

require __DIR__.'/auth.php';
