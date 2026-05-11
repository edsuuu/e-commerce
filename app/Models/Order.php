<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'number',
    'status',
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
    public const string STATUS_PENDING = 'pending';

    public const string STATUS_PAID = 'paid';

    public const string STATUS_SHIPPED = 'shipped';

    public const string STATUS_COMPLETED = 'completed';

    public const string STATUS_CANCELED = 'canceled';

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
