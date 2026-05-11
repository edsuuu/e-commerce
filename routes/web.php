<?php

declare(strict_types=1);

use App\Services\ImageS3;
use Illuminate\Support\Facades\Route;

Route::get('image/{path}/{id}', [ImageS3::class, 'handle'])->where('path', '.*')->name('image-s3');


Route::view('/', 'products.index')->name('home');
Route::view('carrinho', 'cart.show')->name('cart.show');
Route::view('checkout', 'checkout.show')->name('checkout.show');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
