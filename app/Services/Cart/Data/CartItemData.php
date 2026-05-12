<?php

declare(strict_types=1);

namespace App\Services\Cart\Data;

final readonly class CartItemData
{
    public function __construct(
        public int $productId,
        public int $quantity,
    ) {}
}
