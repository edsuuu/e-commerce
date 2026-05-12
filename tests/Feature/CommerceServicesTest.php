<?php

declare(strict_types=1);

use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\Status;
use App\Models\User;
use App\Services\Cart\CartService;
use App\Services\Checkout\CheckoutService;
use App\Services\Checkout\Data\CheckoutData;
use App\Services\Checkout\Exceptions\InvalidOrderStatusTransitionException;
use App\Services\Checkout\Exceptions\StatusNotConfiguredException;
use Database\Seeders\Seeder004Statuses;

function commerceProduct(array $attributes = []): Product
{
    return Product::query()->create([
        'name' => $attributes['name'] ?? 'Produto teste',
        'slug' => $attributes['slug'] ?? fake()->unique()->slug(),
        'sku' => $attributes['sku'] ?? fake()->unique()->bothify('SKU-####'),
        'price_cents' => $attributes['price_cents'] ?? 1000,
        'stock_quantity' => $attributes['stock_quantity'] ?? 10,
        'is_active' => $attributes['is_active'] ?? true,
    ]);
}

function checkoutData(array $attributes = []): array
{
    return [
        'customer_name' => $attributes['customer_name'] ?? 'Cliente Teste',
        'customer_email' => $attributes['customer_email'] ?? 'cliente@example.com',
        'customer_phone' => $attributes['customer_phone'] ?? '',
        'shipping_zipcode' => $attributes['shipping_zipcode'] ?? '01001000',
        'shipping_address' => $attributes['shipping_address'] ?? 'Rua Teste',
        'shipping_number' => $attributes['shipping_number'] ?? '123',
        'shipping_complement' => $attributes['shipping_complement'] ?? '',
        'shipping_district' => $attributes['shipping_district'] ?? 'Centro',
        'shipping_city' => $attributes['shipping_city'] ?? 'Sao Paulo',
        'shipping_state' => $attributes['shipping_state'] ?? 'sp',
    ];
}

test('guest cart stays in session and calculates subtotal', function (): void {
    $product = commerceProduct(['price_cents' => 1500, 'stock_quantity' => 4]);

    resolve(CartService::class)->add($product->id, 2);

    expect(session(CartService::SESSION_KEY))->toHaveKey($product->id)
        ->and(CartItem::query()->count())->toBe(0)
        ->and(resolve(CartService::class)->summary()->subtotalCents)->toBe(3000);
});

test('authenticated cart is persisted in database and receives session cart', function (): void {
    $user = User::factory()->create();
    $product = commerceProduct(['price_cents' => 2500, 'stock_quantity' => 5]);

    session()->put(CartService::SESSION_KEY, [
        $product->id => ['product_id' => $product->id, 'quantity' => 2],
    ]);

    $this->actingAs($user);

    resolve(CartService::class)->add($product->id);

    expect(session()->has(CartService::SESSION_KEY))->toBeFalse()
        ->and(CartItem::query()->whereBelongsTo($user)->where('product_id', $product->id)->value('quantity'))->toBe(3)
        ->and(resolve(CartService::class)->summary()->subtotalCents)->toBe(7500);
});

test('checkout creates order with status and clears authenticated cart', function (): void {
    $this->seed(Seeder004Statuses::class);

    $user = User::factory()->create();
    $product = commerceProduct(['price_cents' => 3000, 'stock_quantity' => 6]);

    $this->actingAs($user);

    resolve(CartService::class)->add($product->id, 2);

    $order = resolve(CheckoutService::class)->createOrder(CheckoutData::fromArray(checkoutData()));

    expect($order->status)->toBe(Order::STATUS_PENDING)
        ->and($order->status_id)->not()->toBeNull()
        ->and(Status::query()->where('type', Status::TYPE_ORDER)->where('name', Order::STATUS_PENDING)->exists())->toBeTrue()
        ->and($order->items()->count())->toBe(1)
        ->and($order->items()->first()?->total_cents)->toBe(6000)
        ->and($product->fresh()->stock_quantity)->toBe(4)
        ->and(resolve(CartService::class)->count())->toBe(0);
});

test('checkout cancellation restores stock once', function (): void {
    $this->seed(Seeder004Statuses::class);

    $user = User::factory()->create();
    $product = commerceProduct(['stock_quantity' => 3]);

    $this->actingAs($user);

    resolve(CartService::class)->add($product->id, 2);
    $order = resolve(CheckoutService::class)->createOrder(CheckoutData::fromArray(checkoutData()));

    $canceled = resolve(CheckoutService::class)->cancel($order);
    $canceledAgain = resolve(CheckoutService::class)->cancel($canceled);

    expect($canceled->status)->toBe(Order::STATUS_CANCELED)
        ->and($product->fresh()->stock_quantity)->toBe(3)
        ->and($canceledAgain->status)->toBe(Order::STATUS_CANCELED)
        ->and($product->fresh()->stock_quantity)->toBe(3);
});

test('checkout blocks invalid status transitions', function (): void {
    $this->seed(Seeder004Statuses::class);

    $user = User::factory()->create();
    $product = commerceProduct();

    $this->actingAs($user);

    resolve(CartService::class)->add($product->id);
    $order = resolve(CheckoutService::class)->createOrder(CheckoutData::fromArray(checkoutData()));

    resolve(CheckoutService::class)->refund($order);
})->throws(InvalidOrderStatusTransitionException::class);

test('checkout fails when required status is not configured', function (): void {
    $user = User::factory()->create();
    $product = commerceProduct();

    $this->actingAs($user);

    resolve(CartService::class)->add($product->id);

    resolve(CheckoutService::class)->createOrder(CheckoutData::fromArray(checkoutData()));
})->throws(StatusNotConfiguredException::class);
