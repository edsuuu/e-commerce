<?php

declare(strict_types=1);

namespace App\Services\Checkout\Exceptions;

final class StatusNotConfiguredException extends CheckoutException
{
    public static function forOrderStatus(string $status): self
    {
        return new self(sprintf('Status de pedido "%s" nao esta configurado no banco.', $status));
    }
}
