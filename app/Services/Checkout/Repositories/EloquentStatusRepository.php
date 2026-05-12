<?php

declare(strict_types=1);

namespace App\Services\Checkout\Repositories;

use App\Models\Status;
use App\Services\Checkout\Contracts\StatusRepositoryInterface;
use App\Services\Checkout\Exceptions\StatusNotConfiguredException;

final class EloquentStatusRepository implements StatusRepositoryInterface
{
    public function orderStatus(string $name): Status
    {
        $status = Status::query()
            ->forType(Status::TYPE_ORDER)
            ->where('name', $name)
            ->first();

        if (! $status instanceof Status) {
            throw StatusNotConfiguredException::forOrderStatus($name);
        }

        return $status;
    }
}
