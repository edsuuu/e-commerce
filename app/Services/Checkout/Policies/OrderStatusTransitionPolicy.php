<?php

declare(strict_types=1);

namespace App\Services\Checkout\Policies;

use App\Models\Order;

final class OrderStatusTransitionPolicy
{
    public function canTransition(string $from, string $to): bool
    {
        if ($from === $to) {
            return true;
        }

        return in_array($to, $this->transitions()[$from] ?? [], true);
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function transitions(): array
    {
        return [
            Order::STATUS_PENDING => [
                Order::STATUS_PAID,
                Order::STATUS_CANCELED,
            ],
            Order::STATUS_PAID => [
                Order::STATUS_SHIPPED,
                Order::STATUS_CANCELED,
                Order::STATUS_REFUND_REQUESTED,
                Order::STATUS_REFUNDED,
            ],
            Order::STATUS_SHIPPED => [
                Order::STATUS_COMPLETED,
                Order::STATUS_DELIVERY_FAILED,
                Order::STATUS_RETURN_REQUESTED,
            ],
            Order::STATUS_DELIVERY_FAILED => [
                Order::STATUS_SHIPPED,
                Order::STATUS_CANCELED,
                Order::STATUS_REFUND_REQUESTED,
                Order::STATUS_REFUNDED,
            ],
            Order::STATUS_COMPLETED => [
                Order::STATUS_RETURN_REQUESTED,
                Order::STATUS_REFUND_REQUESTED,
            ],
            Order::STATUS_RETURN_REQUESTED => [
                Order::STATUS_RETURNED,
                Order::STATUS_REFUND_REQUESTED,
            ],
            Order::STATUS_RETURNED => [
                Order::STATUS_REFUND_REQUESTED,
                Order::STATUS_REFUNDED,
            ],
            Order::STATUS_REFUND_REQUESTED => [
                Order::STATUS_REFUNDED,
            ],
        ];
    }
}
