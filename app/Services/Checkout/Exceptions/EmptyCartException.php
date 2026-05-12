<?php

declare(strict_types=1);

namespace App\Services\Checkout\Exceptions;

final class EmptyCartException extends CheckoutException
{
    public static function make(): self
    {
        return new self('Seu carrinho esta vazio.');
    }
}
