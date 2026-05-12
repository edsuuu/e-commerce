<?php

declare(strict_types=1);

namespace App\Services\Cart;

use App\Models\Product;
use App\Models\User;
use App\Services\Cart\Contracts\CartRepositoryInterface;
use App\Services\Cart\Data\CartItemData;
use App\Services\Cart\Data\CartSummaryData;
use App\Services\Cart\Data\CartSummaryItemData;
use App\Services\Cart\Exceptions\ProductUnavailableException;
use App\Services\Cart\Repositories\DatabaseCartRepository;
use App\Services\Cart\Repositories\SessionCartRepository;

final readonly class CartService
{
    public const string SESSION_KEY = 'cart';

    public function __construct(
        private SessionCartRepository $sessionCartRepository,
        private DatabaseCartRepository $databaseCartRepository,
    ) {}

    public function add(int|Product $product, int $quantity = 1): void
    {
        if ($quantity < 1) {
            return;
        }

        $product = $this->availableProduct($product);
        $repository = $this->repository();
        $currentQuantity = $repository->quantityFor($product->id);

        $repository->put(new CartItemData(
            productId: $product->id,
            quantity: min($currentQuantity + $quantity, $product->stock_quantity),
        ));
    }

    public function increment(int $productId): void
    {
        $this->add($productId);
    }

    public function decrement(int $productId): void
    {
        $this->setQuantity($productId, $this->repository()->quantityFor($productId) - 1);
    }

    public function setQuantity(int $productId, int $quantity): void
    {
        if ($quantity < 1) {
            $this->remove($productId);

            return;
        }

        $product = $this->availableProduct($productId);

        $this->repository()->put(new CartItemData(
            productId: $product->id,
            quantity: min($quantity, $product->stock_quantity),
        ));
    }

    public function remove(int $productId): void
    {
        $this->repository()->remove($productId);
    }

    public function clear(): void
    {
        $this->sessionCartRepository->clear();

        if ($this->user() instanceof User) {
            $this->databaseCartRepository->clear();
        }
    }

    public function count(): int
    {
        return $this->summary()->count();
    }

    public function summary(): CartSummaryData
    {
        $cartItems = $this->items();

        if ($cartItems === []) {
            return new CartSummaryData([], 0);
        }

        $products = Product::query()
            ->with('images')
            ->available()
            ->whereIn('id', array_keys($cartItems))
            ->get()
            ->keyBy('id');

        $summaryItems = [];
        $subtotal = 0;

        foreach ($cartItems as $productId => $cartItem) {
            $product = $products->get($productId);

            if (! $product instanceof Product) {
                $this->remove($productId);

                continue;
            }

            $quantity = min($cartItem->quantity, $product->stock_quantity);

            if ($quantity < 1) {
                $this->remove($productId);

                continue;
            }

            if ($quantity !== $cartItem->quantity) {
                $this->repository()->put(new CartItemData($productId, $quantity));
            }

            $total = $product->price_cents * $quantity;
            $subtotal += $total;

            $summaryItems[] = new CartSummaryItemData($product, $quantity, $total);
        }

        return new CartSummaryData($summaryItems, $subtotal);
    }

    /**
     * @return array<int, CartItemData>
     */
    public function items(): array
    {
        return $this->repository()->items();
    }

    public function migrateSessionCartToDatabase(): void
    {
        if (! $this->user() instanceof User) {
            return;
        }

        $sessionItems = $this->sessionCartRepository->items();

        if ($sessionItems === []) {
            return;
        }

        foreach ($sessionItems as $item) {
            $product = $this->nullableAvailableProduct($item->productId);

            if (! $product instanceof Product) {
                continue;
            }

            $currentQuantity = $this->databaseCartRepository->quantityFor($product->id);

            $this->databaseCartRepository->put(new CartItemData(
                productId: $product->id,
                quantity: min($currentQuantity + $item->quantity, $product->stock_quantity),
            ));
        }

        $this->sessionCartRepository->clear();
    }

    private function repository(): CartRepositoryInterface
    {
        if ($this->user() instanceof User) {
            $this->migrateSessionCartToDatabase();

            return $this->databaseCartRepository;
        }

        return $this->sessionCartRepository;
    }

    private function availableProduct(int|Product $product): Product
    {
        $productId = $product instanceof Product ? $product->id : $product;
        $availableProduct = $this->nullableAvailableProduct($productId);

        if (! $availableProduct instanceof Product) {
            throw ProductUnavailableException::forProduct($productId);
        }

        return $availableProduct;
    }

    private function nullableAvailableProduct(int $productId): ?Product
    {
        return Product::query()->available()->find($productId);
    }

    private function user(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }
}
