<?php

declare(strict_types=1);

namespace App\Services\Cart\Repositories;

use App\Models\CartItem;
use App\Models\User;
use App\Services\Cart\Contracts\CartRepositoryInterface;
use App\Services\Cart\Data\CartItemData;
use App\Services\Cart\Exceptions\AuthenticatedCartRequiresUserException;

final class DatabaseCartRepository implements CartRepositoryInterface
{
    public function items(): array
    {
        return CartItem::query()
            ->whereBelongsTo($this->user())
            ->get(['product_id', 'quantity'])
            ->mapWithKeys(fn (CartItem $item): array => [
                (int) $item->product_id => new CartItemData(
                    productId: (int) $item->product_id,
                    quantity: (int) $item->quantity,
                ),
            ])
            ->all();
    }

    public function put(CartItemData $item): void
    {
        CartItem::query()->updateOrCreate(
            [
                'user_id' => $this->user()->id,
                'product_id' => $item->productId,
            ],
            ['quantity' => $item->quantity],
        );
    }

    public function remove(int $productId): void
    {
        CartItem::query()
            ->whereBelongsTo($this->user())
            ->where('product_id', $productId)
            ->delete();
    }

    public function clear(): void
    {
        CartItem::query()
            ->whereBelongsTo($this->user())
            ->delete();
    }

    public function quantityFor(int $productId): int
    {
        $quantity = CartItem::query()
            ->whereBelongsTo($this->user())
            ->where('product_id', $productId)
            ->value('quantity');

        return is_numeric($quantity) ? (int) $quantity : 0;
    }

    private function user(): User
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            throw AuthenticatedCartRequiresUserException::make();
        }

        return $user;
    }
}
