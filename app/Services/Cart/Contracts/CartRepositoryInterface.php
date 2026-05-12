<?php

declare(strict_types=1);

namespace App\Services\Cart\Contracts;

use App\Services\Cart\Data\CartItemData;

interface CartRepositoryInterface
{
    /**
     * @return array<int, CartItemData>
     */
    public function items(): array;

    public function put(CartItemData $item): void;

    public function remove(int $productId): void;

    public function clear(): void;

    public function quantityFor(int $productId): int;
}
