<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\UploadService;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['product_id', 'file_id', 'file_path', 'alt_text', 'is_primary', 'is_active', 'sort_order'])]
final class ProductFile extends Model
{
    protected $table = 'product_file';

    protected $appends = ['file_path'];

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo<File, $this>
     */
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    protected function getFilePathAttribute(): ?string
    {
        return $this->file?->path;
    }

    protected function setFilePathAttribute(?string $path): void
    {
        if (blank($path)) {
            return;
        }

        $file = resolve(UploadService::class)->fileFromPath($path, $this->alt_text);

        $this->attributes['file_id'] = $file->id;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_primary' => 'boolean',
        ];
    }
}
