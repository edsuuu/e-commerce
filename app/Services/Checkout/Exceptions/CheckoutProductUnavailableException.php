<?php

declare(strict_types=1);

namespace App\Services\Checkout\Exceptions;

final class CheckoutProductUnavailableException extends CheckoutException
{
    public static function forProduct(int $productId): self
    {
        return new self(sprintf('Produto %d indisponivel para checkout.', $productId));
    }
}
