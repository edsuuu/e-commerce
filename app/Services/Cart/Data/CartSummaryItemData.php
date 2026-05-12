<?php

declare(strict_types=1);

namespace App\Services\Cart\Data;

use App\Models\Product;

final readonly class CartSummaryItemData
{
    public function __construct(
        public Product $product,
        public int $quantity,
        public int $totalCents,
    ) {}

    /**
     * @return array{product: Product, quantity: int, total: int}
     */
    public function toViewData(): array
    {
        return [
            'product' => $this->product,
            'quantity' => $this->quantity,
            'total' => $this->totalCents,
        ];
    }
}
