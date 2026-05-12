<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Models\Product;
use App\Services\ImageS3;
use Illuminate\Support\Facades\Route;

Route::get('image/{path}/{id}', ImageS3::handle(...))->where('path', '.*')->name('image-s3');
Route::get('auth/google', [GoogleAuthController::class, 'redirect'])->middleware('guest')->name('auth.google.redirect');
Route::get('auth/google/callback', [GoogleAuthController::class, 'callback'])->middleware('guest')->name('auth.google.callback');

Route::view('/', 'products.index')->name('home');
Route::get('produto/{product:slug}', fn (Product $product) => view('products.show', [
    'product' => $product,
]))->name('products.show');
Route::view('carrinho', 'cart.show')->name('cart.show');
Route::view('checkout', 'checkout.show')->name('checkout.show');

require __DIR__.'/settings.php';
