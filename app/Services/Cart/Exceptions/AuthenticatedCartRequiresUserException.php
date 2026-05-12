<?php

declare(strict_types=1);

namespace App\Services\Cart\Exceptions;

final class AuthenticatedCartRequiresUserException extends CartException
{
    public static function make(): self
    {
        return new self('Carrinho autenticado exige um usuario logado.');
    }
}
