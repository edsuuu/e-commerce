<?php

declare(strict_types=1);

namespace App\Livewire\Cart;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Carrinho')]
final class Show extends Component
{
    public function increment(int $productId): void
    {
        $this->changeQuantity($productId, 1);
    }

    public function decrement(int $productId): void
    {
        $this->changeQuantity($productId, -1);
    }

    public function remove(int $productId): void
    {
        $cart = $this->cart();
        unset($cart[$productId]);

        session()->put('cart', $cart);
        $this->dispatch('cart-updated');
    }

    public function clear(): void
    {
        session()->forget('cart');
        $this->dispatch('cart-updated');
    }

    public function render(): View
    {
        return view('livewire.cart.show', $this->cartData());
    }

    private function changeQuantity(int $productId, int $amount): void
    {
        $product = Product::query()->available()->find($productId);

        if (! $product instanceof Product) {
            $this->remove($productId);

            return;
        }

        $cart = $this->cart();
        $currentItem = $cart[$productId] ?? ['quantity' => 0];
        $quantity = $currentItem['quantity'] + $amount;

        if ($quantity < 1) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = [
                'product_id' => $productId,
                'quantity' => min($quantity, $product->stock_quantity),
            ];
        }

        session()->put('cart', $cart);
        $this->dispatch('cart-updated');
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, subtotal: int}
     */
    private function cartData(): array
    {
        $cart = $this->cart();
        $products = Product::query()
            ->with('images')
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        $items = [];
        $subtotal = 0;

        foreach ($cart as $productId => $item) {
            $product = $products->get($productId);

            if (! $product instanceof Product) {
                continue;
            }

            $quantity = min((int) $item['quantity'], $product->stock_quantity);
            $total = $product->price_cents * $quantity;
            $subtotal += $total;

            $items[] = ['product' => $product, 'quantity' => $quantity, 'total' => $total];
        }

        return ['items' => $items, 'subtotal' => $subtotal];
    }

    /**
     * @return array<int, array{product_id: int, quantity: int}>
     */
    private function cart(): array
    {
        $cart = session()->get('cart', []);

        if (! is_array($cart)) {
            return [];
        }

        $normalized = [];

        foreach ($cart as $productId => $item) {
            if (! is_numeric($productId)) {
                continue;
            }

            if (! is_array($item)) {
                continue;
            }

            if (! isset($item['quantity'])) {
                continue;
            }

            if (! is_numeric($item['quantity'])) {
                continue;
            }

            $rawProductId = $item['product_id'] ?? $productId;

            if (! is_numeric($rawProductId)) {
                continue;
            }

            $normalized[(int) $productId] = [
                'product_id' => (int) $rawProductId,
                'quantity' => (int) $item['quantity'],
            ];
        }

        return $normalized;
    }
}
