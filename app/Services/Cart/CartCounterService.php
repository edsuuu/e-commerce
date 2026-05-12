<?php

declare(strict_types=1);

namespace App\Services\Cart;

final readonly class CartCounterService
{
    public function __construct(private CartService $cartService) {}

    public function count(): int
    {
        return $this->cartService->count();
    }
}
