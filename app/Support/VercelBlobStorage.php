<?php

namespace App\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class VercelBlobStorage
{
    private const API_VERSION = '12';

    public static function store(UploadedFile $file, string $directory): string
    {
        [$token, $storeId] = self::credentials();

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');
        $pathname = trim($directory, '/').'/'.Str::uuid().'.'.$extension;
        $contents = file_get_contents($file->getRealPath());

        if ($contents === false) {
            throw new \RuntimeException('No se pudo leer el archivo subido.');
        }

        $response = self::request($token, $storeId)
            ->withHeaders([
                'x-add-random-suffix' => '0',
                'x-content-length' => (string) strlen($contents),
                'x-content-type' => $file->getMimeType() ?: 'application/octet-stream',
                'x-vercel-blob-access' => config('services.vercel_blob.access', 'public'),
            ])
            ->withBody($contents, 'application/octet-stream')
            ->put(self::apiUrl().'/?'.http_build_query(['pathname' => $pathname]));

        if ($response->failed()) {
            throw new \RuntimeException(self::errorMessage($response->json()));
        }

        $url = $response->json('url');

        if (! is_string($url) || ! self::isBlobUrl($url)) {
            throw new \RuntimeException('Vercel Blob no devolvió una URL válida para el archivo.');
        }

        return $url;
    }

    public static function delete(string $url): void
    {
        if (! self::isBlobUrl($url)) {
            return;
        }

        [$token, $storeId] = self::credentials();
        $response = self::request($token, $storeId)
            ->asJson()
            ->post(self::apiUrl().'/delete', ['urls' => [$url]]);

        if ($response->failed() && $response->status() !== 404) {
            throw new \RuntimeException(self::errorMessage($response->json()));
        }
    }

    public static function isBlobUrl(string $value): bool
    {
        $host = parse_url($value, PHP_URL_HOST);

        return is_string($host)
            && Str::endsWith(strtolower($host), '.blob.vercel-storage.com');
    }

    private static function request(string $token, string $storeId): PendingRequest
    {
        return Http::withToken($token)
            ->acceptJson()
            ->timeout(60)
            ->retry(2, 200, throw: false)
            ->withHeaders([
                'x-api-blob-request-attempt' => '0',
                'x-api-blob-request-id' => $storeId.':'.now()->getTimestampMs().':'.Str::random(12),
                'x-api-version' => self::API_VERSION,
                'x-vercel-blob-store-id' => $storeId,
            ]);
    }

    private static function credentials(): array
    {
        $readWriteToken = trim((string) config('services.vercel_blob.read_write_token'));

        if ($readWriteToken !== '') {
            $parts = explode('_', $readWriteToken);
            $storeId = $parts[3] ?? '';

            if ($storeId === '') {
                throw new \RuntimeException('BLOB_READ_WRITE_TOKEN no tiene un formato válido.');
            }

            return [$readWriteToken, $storeId];
        }

        $oidcToken = trim((string) config('services.vercel_blob.oidc_token'));

        if ($oidcToken === '' && app()->bound('request')) {
            $oidcToken = trim((string) request()->header('x-vercel-oidc-token'));
        }

        $storeId = Str::after(trim((string) config('services.vercel_blob.store_id')), 'store_');

        if ($oidcToken === '' || $storeId === '') {
            throw new \RuntimeException('Vercel Blob no está conectado: faltan BLOB_STORE_ID o VERCEL_OIDC_TOKEN.');
        }

        return [$oidcToken, $storeId];
    }

    private static function apiUrl(): string
    {
        return rtrim((string) config('services.vercel_blob.api_url', 'https://vercel.com/api/blob'), '/');
    }

    private static function errorMessage(mixed $body): string
    {
        $message = is_array($body) ? data_get($body, 'error.message') : null;

        return is_string($message) && $message !== ''
            ? 'Vercel Blob: '.$message
            : 'No se pudo completar la operación en Vercel Blob.';
    }
}
