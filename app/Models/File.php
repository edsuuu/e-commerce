<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\UploadService;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['uuid', 'disk', 'path', 'name', 'mime_type', 'size', 'alt_text', 'is_active'])]
final class File extends Model
{
    /**
     * @return BelongsToMany<Product, $this>
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot(['alt_text', 'is_primary', 'is_active', 'sort_order'])
            ->withTimestamps();
    }

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    protected function getUrlAttribute(): string
    {
        return resolve(UploadService::class)->url($this->path, $this->disk) ?? '';
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
