<?php

declare(strict_types=1);

namespace App\Livewire\Checkout;

use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

        $data = [
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'shipping_zipcode' => $this->shipping_zipcode,
            'shipping_address' => $this->shipping_address,
            'shipping_number' => $this->shipping_number,
            'shipping_complement' => $this->shipping_complement,
            'shipping_district' => $this->shipping_district,
            'shipping_city' => $this->shipping_city,
            'shipping_state' => mb_strtoupper($this->shipping_state),
        ];

        $cart = $this->cartItems();

        if ($cart['items'] === []) {
            $this->addError('cart', 'Seu carrinho está vazio.');

            return;
        }

        $order = DB::transaction(function () use ($cart, $data): Order {
            $order = Order::query()->create([
                ...$data,
                'user_id' => auth()->id(),
                'number' => 'PED-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
                'status' => Order::STATUS_PENDING,
                'subtotal_cents' => $cart['subtotal'],
                'shipping_cents' => 0,
                'discount_cents' => 0,
                'total_cents' => $cart['subtotal'],
            ]);

            foreach ($cart['items'] as $item) {
                $product = Product::query()->lockForUpdate()->findOrFail($item['product']->id);

                $quantity = min($item['quantity'], $product->stock_quantity);

                if ($quantity < 1) {
                    continue;
                }

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'unit_price_cents' => $product->price_cents,
                    'quantity' => $quantity,
                    'total_cents' => $product->price_cents * $quantity,
                ]);

                $product->decrement('stock_quantity', $quantity);
                $product->refresh();

                StockMovement::query()->create([
                    'product_id' => $product->id,
                    'order_id' => $order->id,
                    'user_id' => auth()->id(),
                    'type' => StockMovement::TYPE_OUT,
                    'quantity' => -$quantity,
                    'stock_after' => $product->stock_quantity,
                    'reason' => 'Venda no checkout',
                ]);
            }

            return $order;
        });

        session()->forget('cart');
        $this->completedOrderNumber = $order->number;
        $this->dispatch('cart-updated');
    }

    public function render(): View
    {
        return view('livewire.checkout.show', $this->cartItems());
    }

    /**
     * @return array{items: array<int, array{product: Product, quantity: int, total: int}>, subtotal: int}
     */
    private function cartItems(): array
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
            $items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'total' => $total,
            ];
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
