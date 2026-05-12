<?php

declare(strict_types=1);

namespace App\Services\Checkout;

use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\Cart\CartService;
use App\Services\Cart\Data\CartItemData;
use App\Services\Checkout\Contracts\CheckoutServiceInterface;
use App\Services\Checkout\Contracts\StatusRepositoryInterface;
use App\Services\Checkout\Data\CheckoutData;
use App\Services\Checkout\Data\CheckoutItemData;
use App\Services\Checkout\Data\OrderStatusTransitionData;
use App\Services\Checkout\Exceptions\CheckoutProductUnavailableException;
use App\Services\Checkout\Exceptions\EmptyCartException;
use App\Services\Checkout\Exceptions\InsufficientStockException;
use App\Services\Checkout\Exceptions\InvalidOrderStatusTransitionException;
use App\Services\Checkout\Policies\OrderStatusTransitionPolicy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final readonly class CheckoutService implements CheckoutServiceInterface
{
    public function __construct(
        private CartService $cartService,
        private StatusRepositoryInterface $statusRepository,
        private OrderStatusTransitionPolicy $statusTransitionPolicy,
    ) {}

    public function createOrder(CheckoutData $data): Order
    {
        $cartItems = $this->cartService->items();

        if ($cartItems === []) {
            throw EmptyCartException::make();
        }

        $order = DB::transaction(function () use ($cartItems, $data): Order {
            $preparedItems = $this->prepareOrderItems($cartItems);

            if ($preparedItems === []) {
                throw EmptyCartException::make();
            }

            $subtotal = array_sum(array_map(
                fn (CheckoutItemData $item): int => $item->totalCents,
                $preparedItems,
            ));
            $status = $this->statusRepository->orderStatus(Order::STATUS_PENDING);

            $order = Order::query()->create([
                ...$data->toOrderAttributes(),
                'user_id' => auth()->id(),
                'number' => $this->generateOrderNumber(),
                'status_id' => $status->id,
                'status' => $status->name,
                'subtotal_cents' => $subtotal,
                'shipping_cents' => 0,
                'discount_cents' => 0,
                'total_cents' => $subtotal,
                'metadata' => $this->statusHistory(new OrderStatusTransitionData(
                    from: null,
                    to: $status->name,
                    reason: 'Pedido criado no checkout',
                    userId: $this->userId(),
                )),
            ]);

            foreach ($preparedItems as $item) {
                $order->items()->create([
                    'product_id' => $item->product->id,
                    'product_name' => $item->product->name,
                    'product_sku' => $item->product->sku,
                    'unit_price_cents' => $item->product->price_cents,
                    'quantity' => $item->quantity,
                    'total_cents' => $item->totalCents,
                ]);

                $item->product->decrement('stock_quantity', $item->quantity);
                $item->product->refresh();

                StockMovement::query()->create([
                    'product_id' => $item->product->id,
                    'order_id' => $order->id,
                    'user_id' => auth()->id(),
                    'type' => StockMovement::TYPE_OUT,
                    'quantity' => -$item->quantity,
                    'stock_after' => $item->product->stock_quantity,
                    'reason' => 'Venda no checkout',
                ]);
            }

            return $order;
        });

        $this->cartService->clear();

        return $order;
    }

    public function cancel(Order $order, ?string $reason = null): Order
    {
        return $this->transition($order, Order::STATUS_CANCELED, $reason ?? 'Pedido cancelado');
    }

    public function requestRefund(Order $order, ?string $reason = null): Order
    {
        return $this->transition($order, Order::STATUS_REFUND_REQUESTED, $reason ?? 'Reembolso solicitado');
    }

    public function refund(Order $order, ?string $reason = null): Order
    {
        return $this->transition($order, Order::STATUS_REFUNDED, $reason ?? 'Pedido reembolsado');
    }

    public function markAsNotDelivered(Order $order, ?string $reason = null): Order
    {
        return $this->transition($order, Order::STATUS_DELIVERY_FAILED, $reason ?? 'Pedido nao entregue');
    }

    public function requestReturn(Order $order, ?string $reason = null): Order
    {
        return $this->transition($order, Order::STATUS_RETURN_REQUESTED, $reason ?? 'Devolucao solicitada');
    }

    public function markAsReturned(Order $order, ?string $reason = null): Order
    {
        return $this->transition($order, Order::STATUS_RETURNED, $reason ?? 'Pedido devolvido');
    }

    public function transition(Order $order, string $statusName, ?string $reason = null): Order
    {
        $targetStatus = $this->statusRepository->orderStatus($statusName);

        if (! $this->statusTransitionPolicy->canTransition($order->status, $targetStatus->name)) {
            throw InvalidOrderStatusTransitionException::fromStatus($order->status, $targetStatus->name);
        }

        return DB::transaction(function () use ($order, $targetStatus, $reason): Order {
            /** @var Order $lockedOrder */
            $lockedOrder = Order::query()
                ->with('items')
                ->lockForUpdate()
                ->findOrFail($order->id);

            if (! $this->statusTransitionPolicy->canTransition($lockedOrder->status, $targetStatus->name)) {
                throw InvalidOrderStatusTransitionException::fromStatus($lockedOrder->status, $targetStatus->name);
            }

            $metadata = $this->appendStatusHistory($lockedOrder, new OrderStatusTransitionData(
                from: $lockedOrder->status,
                to: $targetStatus->name,
                reason: $reason,
                userId: $this->userId(),
            ));

            if ($this->shouldRestoreStock($targetStatus->name, $lockedOrder)) {
                $this->restoreStock($lockedOrder, $reason ?? $targetStatus->name);
                $metadata['stock_restored_at'] = now()->toISOString();
            }

            $lockedOrder->forceFill([
                'status_id' => $targetStatus->id,
                'status' => $targetStatus->name,
                'metadata' => $metadata,
            ])->save();

            return $lockedOrder->refresh();
        });
    }

    /**
     * @param  array<int, CartItemData>  $cartItems
     * @return array<int, CheckoutItemData>
     */
    private function prepareOrderItems(array $cartItems): array
    {
        $items = [];

        foreach ($cartItems as $item) {
            $product = Product::query()
                ->available()
                ->whereKey($item->productId)
                ->lockForUpdate()
                ->first();

            if (! $product instanceof Product) {
                throw CheckoutProductUnavailableException::forProduct($item->productId);
            }

            if ($item->quantity > $product->stock_quantity) {
                throw InsufficientStockException::forProduct(
                    productId: $product->id,
                    requested: $item->quantity,
                    available: $product->stock_quantity,
                );
            }

            $items[] = new CheckoutItemData(
                product: $product,
                quantity: $item->quantity,
                totalCents: $product->price_cents * $item->quantity,
            );
        }

        return $items;
    }

    private function generateOrderNumber(): string
    {
        return 'PED-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
    }

    /**
     * @return array<string, mixed>
     */
    private function statusHistory(OrderStatusTransitionData $transition): array
    {
        return [
            'status_history' => [$this->transitionToArray($transition)],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function appendStatusHistory(Order $order, OrderStatusTransitionData $transition): array
    {
        $metadata = $order->metadata;

        if (! is_array($metadata)) {
            $metadata = [];
        }

        $history = $metadata['status_history'] ?? [];

        if (! is_array($history)) {
            $history = [];
        }

        $history[] = $this->transitionToArray($transition);
        $metadata['status_history'] = $history;

        return $metadata;
    }

    /**
     * @return array{from: string|null, to: string, reason: string|null, user_id: int|null, created_at: string}
     */
    private function transitionToArray(OrderStatusTransitionData $transition): array
    {
        return [
            'from' => $transition->from,
            'to' => $transition->to,
            'reason' => $transition->reason,
            'user_id' => $transition->userId,
            'created_at' => now()->format('c'),
        ];
    }

    private function userId(): ?int
    {
        $userId = auth()->id();

        return is_numeric($userId) ? (int) $userId : null;
    }

    private function shouldRestoreStock(string $statusName, Order $order): bool
    {
        $metadata = $order->metadata;

        if (is_array($metadata) && array_key_exists('stock_restored_at', $metadata)) {
            return false;
        }

        return in_array($statusName, [
            Order::STATUS_CANCELED,
            Order::STATUS_RETURNED,
            Order::STATUS_REFUNDED,
        ], true);
    }

    private function restoreStock(Order $order, string $reason): void
    {
        foreach ($order->items as $item) {
            if ($item->product_id === null) {
                continue;
            }

            $product = Product::query()->lockForUpdate()->find($item->product_id);

            if (! $product instanceof Product) {
                continue;
            }

            $product->increment('stock_quantity', $item->quantity);
            $product->refresh();

            StockMovement::query()->create([
                'product_id' => $product->id,
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'type' => StockMovement::TYPE_IN,
                'quantity' => $item->quantity,
                'stock_after' => $product->stock_quantity,
                'reason' => $reason,
            ]);
        }
    }
}
