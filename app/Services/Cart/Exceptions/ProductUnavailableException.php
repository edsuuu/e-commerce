<?php

declare(strict_types=1);

namespace App\Services\Cart\Exceptions;

final class ProductUnavailableException extends CartException
{
    public static function forProduct(int $productId): self
    {
        return new self(sprintf('Produto %d indisponivel para carrinho.', $productId));
    }
}
