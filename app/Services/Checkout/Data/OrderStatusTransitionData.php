<?php

declare(strict_types=1);

namespace App\Services\Checkout\Data;

final readonly class OrderStatusTransitionData
{
    public function __construct(
        public ?string $from,
        public string $to,
        public ?string $reason,
        public ?int $userId,
    ) {}
}
