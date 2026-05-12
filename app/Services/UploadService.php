<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;
use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Throwable;

final class UploadService
{
    public function disk(): string
    {
        $disk = config('filesystems.default');

        return is_string($disk) ? $disk : 'public';
    }

    public function url(?string $path, ?string $disk = null): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '/'])) {
            return $path;
        }

        $disk ??= $this->disk();

        if ($disk === 's3') {
            return route('image-s3', [
                'path' => dirname($path),
                'id' => basename($path),
            ]);
        }

        return Storage::disk($disk)->url($path);
    }

    /**
     * @param  array<int, string|null>  $segments
     *
     * @throws Throwable
     */
    public function directory(string $path, array $segments = []): string
    {
        throw_if(blank($path), InvalidArgumentException::class, 'O path do upload precisa ser informado.');

        $parts = [
            $path,
            ...array_filter($segments, filled(...)),
        ];

        return collect($parts)
            ->map(fn (string $segment): string => Str::of($segment)
                ->ascii()
                ->lower()
                ->replace(['\\', '_'], '/')
                ->explode('/')
                ->filter()
                ->map(fn (string $part): string => Str::slug($part))
                ->implode('/'))
            ->filter()
            ->implode('/');
    }

    /**
     * @throws Throwable
     */
    public function productDirectoryForCategoryId(?int $categoryId): string
    {
        $categorySlug = Category::query()
            ->whereKey($categoryId)
            ->value('slug');

        return $this->directory('produtos', [
            is_string($categorySlug) && filled($categorySlug) ? $categorySlug : 'sem-categoria',
        ]);
    }

    public function categoryDirectory(): string
    {
        return $this->directory('categorias');
    }

    public function userDirectory(): string
    {
        return $this->directory('usuarios');
    }

    public function fileFromPath(string $path, ?string $altText = null): File
    {
        Log::info('Tentando salvar arquivo no banco:', ['path' => $path]);

        $file = File::query()->firstOrNew(['path' => $path]);

        if (! $file->exists) {
            $file->uuid = (string) Str::uuid();
        }

        $file->fill([
            'disk' => $this->disk(),
            'name' => basename($path),
            'alt_text' => $altText,
            'is_active' => true,
        ]);

        $file->save();

        Log::info('Arquivo salvo com sucesso:', ['id' => $file->id, 'path' => $file->path]);

        return $file;
    }

    public function fileByUuid(string $uuid): ?File
    {
        return File::query()
            ->where('uuid', $uuid)
            ->first();
    }

    /**
     * @throws Throwable
     */
    public function store(UploadedFile $file, string $path): string
    {
        $storedPath = $file->storePublicly($this->directory($path), [
            'disk' => $this->disk(),
        ]);

        throw_if($storedPath === false, InvalidArgumentException::class, 'Nao foi possivel armazenar o arquivo enviado.');

        return $storedPath;
    }
}
