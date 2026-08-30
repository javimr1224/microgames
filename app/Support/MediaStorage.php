<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaStorage
{
    private const PREFIX = 'uploads://';

    public static function store(UploadedFile $file, string $directory): string
    {
        if (config('filesystems.disks.uploads.driver') === 'vercel-blob') {
            return VercelBlobStorage::store($file, $directory);
        }

        $visibility = config('filesystems.disks.uploads.visibility', 'public');
        $path = Storage::disk('uploads')->putFile($directory, $file, $visibility);

        if (! $path) {
            throw new \RuntimeException('No se pudo guardar el archivo subido.');
        }

        return self::PREFIX.$path;
    }

    public static function delete(?string $value): void
    {
        if ($value && VercelBlobStorage::isBlobUrl($value)) {
            VercelBlobStorage::delete($value);

            return;
        }

        if (! $value || ! Str::startsWith($value, self::PREFIX)) {
            return;
        }

        Storage::disk('uploads')->delete(Str::after($value, self::PREFIX));
    }

    public static function url(?string $value, ?string $legacyDirectory = null): ?string
    {
        if (! $value) {
            return null;
        }

        if (Str::startsWith($value, self::PREFIX)) {
            return Storage::disk('uploads')->url(Str::after($value, self::PREFIX));
        }

        if (Str::startsWith($value, ['http://', 'https://', 'data:'])) {
            return $value;
        }

        $path = ltrim($value, '/');

        if ($legacyDirectory && ! Str::contains($path, '/')) {
            $path = trim($legacyDirectory, '/').'/'.$path;
        }

        return asset($path);
    }
}
