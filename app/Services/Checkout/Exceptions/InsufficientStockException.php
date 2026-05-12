<?php

declare(strict_types=1);

namespace App\Services\Checkout\Exceptions;

final class InsufficientStockException extends CheckoutException
{
    public static function forProduct(int $productId, int $requested, int $available): self
    {
        return new self(sprintf(
            'Produto %d possui estoque insuficiente. Solicitado: %d. Disponivel: %d.',
            $productId,
            $requested,
            $available,
        ));
    }
}
