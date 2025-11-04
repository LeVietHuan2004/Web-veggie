<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CategoryControlller;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\DeliveryController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Middleware\DefaultAdminData;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::middleware(['check.auth.admin'])->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
    });

    Route::get('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    Route::middleware(['auth.custom', DefaultAdminData::class])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        Route::get('/profile', [AccountController::class, 'index'])->name('admin.profile');
        Route::post('/profile/update', [AccountController::class, 'updateProfile']);
        Route::get('/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
        Route::post('/notification/update', [NotificationController::class, 'update']);

        Route::middleware(['permission:manage_users'])->group(function () {
            Route::get('/users', [UsersController::class, 'index'])->name('admin.users.index');
            Route::post('/user/upgrade', [UsersController::class, 'upgrade']);
            Route::post('/user/updateStatus', [UsersController::class, 'updateStatus']);
        });

        Route::middleware(['permission:manage_categories'])->group(function () {
            Route::get('/categories/add', [CategoryControlller::class, 'showFormAddCate'])->name('admin.categories.add');
            Route::post('/categories/add', [CategoryControlller::class, 'addCategory'])->name('admin.categories.add');

            Route::get('/categories', [CategoryControlller::class, 'index'])->name('admin.categories.index');
            Route::post('/categories/update', [CategoryControlller::class, 'updateCategory']);
            Route::post('/categories/delete', [CategoryControlller::class, 'deleteCategory']);
        });

        Route::middleware(['permission:manage_products'])->group(function () {
            Route::get('/product/add', [ProductController::class, 'showFormAddProduct'])->name('admin.product.add');
            Route::post('/product/add', [ProductController::class, 'addProduct'])->name('admin.product.add');

            Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
            Route::post('/product/update', [ProductController::class, 'updateProduct']);
            Route::post('/product/delete', [ProductController::class, 'deleteProduct']);
            Route::get('/coupons', [CouponController::class, 'index'])->name('admin.coupons.index');
            Route::post('/coupons', [CouponController::class, 'store'])->name('admin.coupons.store');
            Route::put('/coupons/{coupon}', [CouponController::class, 'update'])->name('admin.coupons.update');
            Route::delete('/coupons/{coupon}', [CouponController::class, 'destroy'])->name('admin.coupons.destroy');
        });

        Route::middleware(['permission:manage_orders'])->group(function () {
            Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');
            Route::post('/order/confirm', [OrderController::class, 'confrimOrder']);
            Route::post('/order/ready', [OrderController::class, 'markReadyForDelivery']);

            Route::get('/order-detail/{id}', [OrderController::class, 'showOrderDetail'])->name('admin.order-detail');
            Route::post('/order-detail/send-invoice', [OrderController::class, 'sendMailInvoice']);
            Route::post('/order-detail/cancel-order', [OrderController::class, 'cancelOrder']);
        });

        Route::middleware(['permission:manage_deliveries'])->group(function () {
            Route::get('/deliveries', [DeliveryController::class, 'index'])->name('admin.deliveries.index');
            Route::post('/deliveries/start', [DeliveryController::class, 'startDelivery']);
            Route::post('/deliveries/complete', [DeliveryController::class, 'completeDelivery']);
        });

        Route::middleware(['permission:manage_contacts'])->group(function () {
            Route::get('/contacts', [ContactController::class, 'index'])->name('admin.contacts.index');
            Route::post('/contact/reply', [ContactController::class, 'replyContact']);
        });
    });
});
?>
