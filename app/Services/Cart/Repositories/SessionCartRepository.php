<?php

declare(strict_types=1);

namespace App\Services\Cart\Repositories;

use App\Services\Cart\CartService;
use App\Services\Cart\Contracts\CartRepositoryInterface;
use App\Services\Cart\Data\CartItemData;

final class SessionCartRepository implements CartRepositoryInterface
{
    public function items(): array
    {
        $cart = session()->get(CartService::SESSION_KEY, []);

        if (! is_array($cart)) {
            return [];
        }

        $items = [];

        foreach ($cart as $productId => $item) {
            if (! is_numeric($productId)) {
                continue;
            }

            if (! is_array($item)) {
                continue;
            }

            $rawProductId = $item['product_id'] ?? $productId;
            $quantity = $item['quantity'] ?? null;
            if (! is_numeric($rawProductId)) {
                continue;
            }

            if (! is_numeric($quantity)) {
                continue;
            }

            $quantity = (int) $quantity;

            if ($quantity < 1) {
                continue;
            }

            $productId = (int) $rawProductId;
            $items[$productId] = new CartItemData($productId, $quantity);
        }

        return $items;
    }

    public function put(CartItemData $item): void
    {
        $items = $this->items();
        $items[$item->productId] = $item;

        session()->put(CartService::SESSION_KEY, $this->toSessionPayload($items));
    }

    public function remove(int $productId): void
    {
        $items = $this->items();
        unset($items[$productId]);

        if ($items === []) {
            $this->clear();

            return;
        }

        session()->put(CartService::SESSION_KEY, $this->toSessionPayload($items));
    }

    public function clear(): void
    {
        session()->forget(CartService::SESSION_KEY);
    }

    public function quantityFor(int $productId): int
    {
        return $this->items()[$productId]->quantity ?? 0;
    }

    /**
     * @param  array<int, CartItemData>  $items
     * @return array<int, array{product_id: int, quantity: int}>
     */
    private function toSessionPayload(array $items): array
    {
        return array_map(
            fn (CartItemData $item): array => [
                'product_id' => $item->productId,
                'quantity' => $item->quantity,
            ],
            $items,
        );
    }
}
