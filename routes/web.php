<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SpecialOfferController;
use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
Route::get('/special-offers', [SpecialOfferController::class, 'index'])->name('special-offers.index');
Route::get('/category/{category:slug}', [CategoryController::class, 'show'])->name('category.show');
Route::view('/about', 'about')->name('about');

// Auth
Route::get('/login', [AuthController::class, 'redirectToLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Cart is available to guests (session based) and merged into the account on login.
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/{item}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{item}', [CartController::class, 'destroy'])->name('cart.destroy');

Route::middleware('auth')->group(function () {
    Route::post('/wishlist/{product}/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::post('/product/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/payment/{order}/pay', [PaymentController::class, 'pay'])->name('payment.pay');
    Route::get('/order/{order}/confirmation', [PaymentController::class, 'confirmation'])->name('order.confirmation');

    Route::prefix('panel')->name('panel.')->group(function () {
        Route::get('/', [PanelController::class, 'index'])->name('dashboard');
        Route::get('/orders', [PanelController::class, 'orders'])->name('orders');
        Route::get('/orders/{order}', [PanelController::class, 'orderShow'])->name('orders.show');
        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
        Route::get('/profile', [PanelController::class, 'profile'])->name('profile');
        Route::put('/profile', [PanelController::class, 'updateProfile'])->name('profile.update');
        Route::resource('addresses', AddressController::class)->except('show');
    });
});

// ─── Admin Panel ─────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', Admin\ProductController::class);
    Route::post('products/{product}/duplicate', [Admin\ProductController::class, 'duplicate'])->name('products.duplicate');
    Route::post('products/{product}/toggle-active', [Admin\ProductController::class, 'toggleActive'])->name('products.toggle-active');

    Route::get('special-products', [Admin\SpecialProductController::class, 'index'])->name('special-products.index');
    Route::post('special-products', [Admin\SpecialProductController::class, 'store'])->name('special-products.store');
    Route::delete('special-products/{product}', [Admin\SpecialProductController::class, 'destroy'])->name('special-products.destroy');

    Route::resource('stories', Admin\StoryController::class)->except(['show']);
    Route::post('stories/{story}/toggle-active', [Admin\StoryController::class, 'toggleActive'])->name('stories.toggle-active');

    Route::resource('banners', Admin\BannerController::class)->except(['show']);
    Route::post('banners/{banner}/toggle-active', [Admin\BannerController::class, 'toggleActive'])->name('banners.toggle-active');

    Route::resource('categories', Admin\CategoryController::class)->except(['show']);

    Route::get('users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::patch('users/{user}/toggle-admin', [Admin\UserController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::patch('users/{user}/toggle-block', [Admin\UserController::class, 'toggleBlock'])->name('users.toggle-block');
    Route::delete('users/{user}', [Admin\UserController::class, 'destroy'])->name('users.destroy');

    Route::get('orders', [Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');

    Route::resource('discounts', Admin\DiscountController::class)->except(['show']);
    Route::patch('discounts/{discount}/toggle', [Admin\DiscountController::class, 'toggle'])->name('discounts.toggle');

    Route::resource('posts', Admin\PostController::class)->except(['show']);
    Route::post('post-images', Admin\PostImageController::class)->name('post-images.store');

    Route::get('contact-messages', [Admin\ContactMessageController::class, 'index'])->name('contact-messages.index');
    Route::get('contact-messages/{contactMessage}', [Admin\ContactMessageController::class, 'show'])->name('contact-messages.show');
    Route::post('contact-messages/{contactMessage}/reply', [Admin\ContactMessageController::class, 'reply'])->name('contact-messages.reply');
    Route::delete('contact-messages/{contactMessage}', [Admin\ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');

    Route::get('settings', [Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [Admin\SettingController::class, 'update'])->name('settings.update');

    Route::get('account', [Admin\AccountController::class, 'edit'])->name('account.edit');
    Route::put('account', [Admin\AccountController::class, 'update'])->name('account.update');
});

// The gateway redirects the browser back here; must stay outside the auth
// group since some drivers use a plain GET/POST without an authenticated session guarantee.
Route::match(['get', 'post'], '/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
