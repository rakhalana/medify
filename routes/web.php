<?php

use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CatalogController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public Catalog & Product Routes
Route::get('/', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/products/{product:slug}', [CatalogController::class, 'show'])->name('catalog.show');
Route::get('/ajax/products/suggest', [CatalogController::class, 'suggest'])->name('ajax.products.suggest');
Route::get('/ajax/products/{product}', [CatalogController::class, 'quickView'])->name('ajax.products.quick-view');

// Shopping Cart Routes (Web & Internal AJAX)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::prefix('ajax/cart')->name('ajax.cart.')->group(function () {
    Route::get('/', [CartController::class, 'getCart'])->name('get');
    Route::post('/items', [CartController::class, 'addItem'])->name('items.add');
    Route::patch('/items/{cartItem}', [CartController::class, 'updateItem'])->name('items.update');
    Route::delete('/items/{cartItem}', [CartController::class, 'removeItem'])->name('items.remove');
    Route::delete('/', [CartController::class, 'clearCart'])->name('clear');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
