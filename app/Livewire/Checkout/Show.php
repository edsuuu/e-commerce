<?php

declare(strict_types=1);

namespace App\Livewire\Checkout;

use App\Services\Cart\CartService;
use App\Services\Checkout\CheckoutService;
use App\Services\Checkout\Data\CheckoutData;
use App\Services\Checkout\Exceptions\CheckoutException;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Checkout')]
final class Show extends Component
{
    public string $customer_name = '';

    public string $customer_email = '';

    public string $customer_phone = '';

    public string $shipping_zipcode = '';

    public string $shipping_address = '';

    public string $shipping_number = '';

    public string $shipping_complement = '';

    public string $shipping_district = '';

    public string $shipping_city = '';

    public string $shipping_state = '';

    public ?string $completedOrderNumber = null;

    public function mount(): void
    {
        $user = auth()->user();

        if ($user) {
            $this->customer_name = $user->name;
            $this->customer_email = $user->email;
        }
    }

    public function placeOrder(): void
    {
        $this->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'shipping_zipcode' => ['required', 'string', 'max:20'],
            'shipping_address' => ['required', 'string', 'max:255'],
            'shipping_number' => ['nullable', 'string', 'max:30'],
            'shipping_complement' => ['nullable', 'string', 'max:255'],
            'shipping_district' => ['nullable', 'string', 'max:120'],
            'shipping_city' => ['required', 'string', 'max:120'],
            'shipping_state' => ['required', 'string', 'size:2'],
        ]);

        try {
            $order = resolve(CheckoutService::class)->createOrder(CheckoutData::fromArray([
                'customer_name' => $this->customer_name,
                'customer_email' => $this->customer_email,
                'customer_phone' => $this->customer_phone,
                'shipping_zipcode' => $this->shipping_zipcode,
                'shipping_address' => $this->shipping_address,
                'shipping_number' => $this->shipping_number,
                'shipping_complement' => $this->shipping_complement,
                'shipping_district' => $this->shipping_district,
                'shipping_city' => $this->shipping_city,
                'shipping_state' => $this->shipping_state,
            ]));
        } catch (CheckoutException $checkoutException) {
            $this->addError('cart', $checkoutException->getMessage());

            return;
        }

        $this->completedOrderNumber = $order->number;
        $this->dispatch('cart-updated');
    }

    public function render(): View
    {
        return view('livewire.checkout.show', resolve(CartService::class)->summary()->toViewData());
    }
}
