<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\UploadService;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'category_id',
    'name',
    'slug',
    'sku',
    'short_description',
    'description',
    'price_cents',
    'compare_at_price_cents',
    'stock_quantity',
    'low_stock_threshold',
    'is_active',
    'is_featured',
    'weight_kg',
    'attributes',
    'published_at',
])]
final class Product extends Model
{
    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany<ProductFile, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductFile::class)
            ->with('file')
            ->where('is_active', true)
            ->orderByDesc('is_primary')
            ->orderBy('sort_order');
    }

    /**
     * @return HasMany<ProductFile, $this>
     */
    public function productFiles(): HasMany
    {
        return $this->hasMany(ProductFile::class)
            ->with('file')
            ->orderByDesc('is_primary')
            ->orderBy('sort_order');
    }

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * @return HasMany<StockMovement, $this>
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    protected static function booted(): void
    {
        self::deleting(function (): void {
            abort(422, 'Produtos nao podem ser apagados. Inative o produto para remove-lo da loja.');
        });
    }

    protected function getFormattedPriceAttribute(): string
    {
        return 'R$ '.number_format($this->price_cents / 100, 2, ',', '.');
    }

    protected function getPrimaryImageUrlAttribute(): string
    {
        $path = $this->images->first()?->file?->path;

        return resolve(UploadService::class)->url($path) ?? 'https://placehold.co/640x480/f3f4f6/111827?text=Produto';
    }

    /**
     * @param  Builder<Product>  $query
     */
    #[Scope]
    protected function available(Builder $query): void
    {
        $query
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->where(fn (Builder $query) => $query
                ->whereNull('published_at')
                ->orWhere('published_at', '<=', now()));
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'attributes' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'weight_kg' => 'decimal:3',
        ];
    }
}
