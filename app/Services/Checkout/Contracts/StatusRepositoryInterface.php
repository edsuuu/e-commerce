<?php

declare(strict_types=1);

namespace App\Services\Checkout\Contracts;

use App\Models\Status;

interface StatusRepositoryInterface
{
    public function orderStatus(string $name): Status;
}
