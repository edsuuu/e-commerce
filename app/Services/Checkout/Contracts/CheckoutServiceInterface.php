<?php

declare(strict_types=1);

namespace App\Services\Checkout\Contracts;

use App\Models\Order;
use App\Services\Checkout\Data\CheckoutData;

interface CheckoutServiceInterface
{
    public function createOrder(CheckoutData $data): Order;

    public function cancel(Order $order, ?string $reason = null): Order;

    public function requestRefund(Order $order, ?string $reason = null): Order;

    public function refund(Order $order, ?string $reason = null): Order;

    public function markAsNotDelivered(Order $order, ?string $reason = null): Order;

    public function requestReturn(Order $order, ?string $reason = null): Order;

    public function markAsReturned(Order $order, ?string $reason = null): Order;

    public function transition(Order $order, string $statusName, ?string $reason = null): Order;
}
