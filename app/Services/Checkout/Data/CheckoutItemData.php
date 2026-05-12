<?php

declare(strict_types=1);

namespace App\Services\Checkout\Data;

use App\Models\Product;

final readonly class CheckoutItemData
{
    public function __construct(
        public Product $product,
        public int $quantity,
        public int $totalCents,
    ) {}
}
