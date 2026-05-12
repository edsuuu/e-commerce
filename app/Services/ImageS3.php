<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

final class ImageS3
{
    public static function handle(string $path, string $id): Response
    {
        $url = sprintf('%s/%s', $path, $id);
        $pathExists = Storage::disk('s3')->exists($url);

        abort_unless($pathExists, 404);

        $content = Storage::disk('s3')->get($url);
        $mime = Storage::disk('s3')->mimeType($url) ?: 'application/octet-stream';

        return response($content)
            ->header('Content-Type', $mime)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Content-Disposition', 'inline; filename="'.basename($id).'"');
    }
}
