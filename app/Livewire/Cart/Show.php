<?php

declare(strict_types=1);

namespace App\Livewire\Cart;

use App\Services\Cart\CartService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Carrinho')]
final class Show extends Component
{
    public function mount(): void
    {
        resolve(CartService::class)->migrateSessionCartToDatabase();
    }

    public function increment(int $productId): void
    {
        resolve(CartService::class)->increment($productId);
        $this->dispatch('cart-updated');
    }

    public function decrement(int $productId): void
    {
        resolve(CartService::class)->decrement($productId);
        $this->dispatch('cart-updated');
    }

    public function remove(int $productId): void
    {
        resolve(CartService::class)->remove($productId);
        $this->dispatch('cart-updated');
    }

    public function clear(): void
    {
        resolve(CartService::class)->clear();
        $this->dispatch('cart-updated');
    }

    public function render(): View
    {
        return view('livewire.cart.show', resolve(CartService::class)->summary()->toViewData());
    }
}
