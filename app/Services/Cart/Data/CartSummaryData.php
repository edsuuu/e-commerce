<?php

declare(strict_types=1);

namespace App\Services\Cart\Data;

use App\Models\Product;

final readonly class CartSummaryData
{
    /**
     * @param  array<int, CartSummaryItemData>  $items
     */
    public function __construct(
        public array $items,
        public int $subtotalCents,
    ) {}

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return array{items: array<int, array{product: Product, quantity: int, total: int}>, subtotal: int}
     */
    public function toViewData(): array
    {
        return [
            'items' => array_map(
                fn (CartSummaryItemData $item): array => $item->toViewData(),
                $this->items,
            ),
            'subtotal' => $this->subtotalCents,
        ];
    }
}
