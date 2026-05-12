<?php

declare(strict_types=1);

namespace App\Services\Checkout\Exceptions;

final class InvalidOrderStatusTransitionException extends CheckoutException
{
    public static function fromStatus(string $from, string $to): self
    {
        return new self(sprintf('Nao e possivel mover o pedido de "%s" para "%s".', $from, $to));
    }
}
