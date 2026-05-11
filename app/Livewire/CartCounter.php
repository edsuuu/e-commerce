<?php

declare(strict_types=1);

namespace App\Livewire;

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
        $cart = session()->get('cart', []);
        $this->count = is_array($cart) ? count($cart) : 0;
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.cart-counter');
    }
}
