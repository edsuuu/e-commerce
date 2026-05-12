<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property array<string, mixed>|null $metadata
 * @property string $status
 */
#[Fillable([
    'user_id',
    'number',
    'status',
    'status_id',
    'customer_name',
    'customer_email',
    'customer_phone',
    'shipping_zipcode',
    'shipping_address',
    'shipping_number',
    'shipping_complement',
    'shipping_district',
    'shipping_city',
    'shipping_state',
    'subtotal_cents',
    'shipping_cents',
    'discount_cents',
    'total_cents',
    'metadata',
])]
final class Order extends Model
{
    public const string STATUS_PENDING = 'Pendente';

    public const string STATUS_PAID = 'Pago';

    public const string STATUS_SHIPPED = 'Enviado';

    public const string STATUS_COMPLETED = 'Concluido';

    public const string STATUS_CANCELED = 'Cancelado';

    public const string STATUS_DELIVERY_FAILED = 'Nao entregue';

    public const string STATUS_RETURN_REQUESTED = 'Devolucao solicitada';

    public const string STATUS_RETURNED = 'Devolvido';

    public const string STATUS_REFUND_REQUESTED = 'Reembolso solicitado';

    public const string STATUS_REFUNDED = 'Reembolsado';

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Status, $this>
     */
    public function statusRecord(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    protected function getFormattedTotalAttribute(): string
    {
        return 'R$ '.number_format($this->total_cents / 100, 2, ',', '.');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }
}
