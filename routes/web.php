<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

/*
|--------------------------------------------------------------------------|
| Storefront (locale-aware)                                                |
|--------------------------------------------------------------------------|
*/

Route::group(['prefix' => '{locale}', 'where' => ['locale' => 'nb|en']], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/butikk', [ProductController::class, 'index'])->name('products.index');
    Route::get('/butikk/{category}', [ProductController::class, 'category'])->name('products.category');
    Route::get('/produkt/{slug}', [ProductController::class, 'show'])->name('products.show');

    Route::get('/handlekurv', [CartController::class, 'index'])->name('cart.index');
    Route::post('/handlekurv/legg-til/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/handlekurv/oppdater/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/handlekurv/fjern/{id}', [CartController::class, 'remove'])->name('cart.remove');

    Route::get('/kasse', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/kasse', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/ordre/{token}', [OrderController::class, 'show'])->name('order.show');

    Route::get('/kontakt', [ContactController::class, 'index'])->name('contact.index');
    Route::post('/kontakt', [ContactController::class, 'store'])->name('contact.store');

    Route::get('/om-oss', fn () => view('pages.om-oss'))->name('pages.about');
    Route::get('/frakt-og-retur', fn () => view('pages.frakt-og-retur'))->name('pages.shipping');
    Route::get('/vilkar', fn () => view('pages.vilkar'))->name('pages.terms');
    Route::get('/personvern', fn () => view('pages.personvern'))->name('pages.privacy');
    Route::get('/informasjonskapsler', fn () => view('pages.cookies'))->name('pages.cookies');
    Route::get('/angrerett', fn () => view('pages.angrerett'))->name('pages.withdrawal');

    // Language switch — keep the user on the equivalent page.
    Route::get('/sprak/{lang}', [LocaleController::class, 'switch'])->name('locale.switch');
});

// Default locale redirect for bare domain.
Route::get('/', function () {
    return redirect()->to('/'.config('app.locale', 'nb'));
})->name('root');

// Webhooks (no locale).
Route::post('/webhooks/btcpay', [\App\Http\Controllers\WebhookController::class, 'btcpay'])->name('webhooks.btcpay');

// Admin.
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', AdminProductController::class);
});
