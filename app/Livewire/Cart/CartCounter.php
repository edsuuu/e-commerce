<?php

declare(strict_types=1);

namespace App\Livewire\Cart;

use App\Services\Cart\CartCounterService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

final class CartCounter extends Component
{
    public int $count = 0;

    public function mount(): void
    {
        $this->updateCount();
    }

    #[On('cart-updated')]
    public function updateCount(): void
    {
        $this->count = resolve(CartCounterService::class)->count();
    }

    public function render(): View
    {
        return view('livewire.cart.cart-counter');
    }
}
