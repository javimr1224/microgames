<?php

use App\Support\MediaStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(TestCase::class);

test('uploaded media is stored and deleted through the configured disk', function () {
    Storage::fake('uploads');
    config()->set('filesystems.disks.uploads.driver', 'local');
    config()->set('filesystems.disks.uploads.visibility', 'public');

    $reference = MediaStorage::store(
        UploadedFile::fake()->create('avatar.png', 10, 'image/png'),
        'avatars'
    );

    expect($reference)->toStartWith('uploads://avatars/');

    $path = str_replace('uploads://', '', $reference);
    Storage::disk('uploads')->assertExists($path);

    MediaStorage::delete($reference);
    Storage::disk('uploads')->assertMissing($path);
});

test('uploaded media is stored and deleted through vercel blob', function () {
    config()->set('filesystems.disks.uploads.driver', 'vercel-blob');
    config()->set('services.vercel_blob.read_write_token', 'vercel_blob_rw_teststore_secret');
    config()->set('services.vercel_blob.store_id', null);
    config()->set('services.vercel_blob.api_url', 'https://vercel.com/api/blob');

    $blobUrl = 'https://teststore.public.blob.vercel-storage.com/images/test.png';

    Http::fake([
        'https://vercel.com/api/blob/?*' => Http::response([
            'url' => $blobUrl,
            'pathname' => 'images/test.png',
        ]),
        'https://vercel.com/api/blob/delete' => Http::response([], 200),
    ]);

    $reference = MediaStorage::store(
        UploadedFile::fake()->create('game.png', 10, 'image/png'),
        'images'
    );

    expect($reference)->toBe($blobUrl)
        ->and(MediaStorage::url($reference))->toBe($blobUrl);

    MediaStorage::delete($reference);

    Http::assertSentCount(2);
    Http::assertSent(fn ($request) => $request->method() === 'PUT'
        && $request->hasHeader('x-vercel-blob-store-id', 'teststore')
        && $request->hasHeader('x-vercel-blob-access', 'public'));
    Http::assertSent(fn ($request) => $request->method() === 'POST'
        && $request->url() === 'https://vercel.com/api/blob/delete');
});

test('vercel blob uses the oidc token from the runtime request header', function () {
    config()->set('filesystems.disks.uploads.driver', 'vercel-blob');
    config()->set('services.vercel_blob.read_write_token', null);
    config()->set('services.vercel_blob.oidc_token', null);
    config()->set('services.vercel_blob.store_id', 'store_teststore');
    config()->set('services.vercel_blob.api_url', 'https://vercel.com/api/blob');
    request()->headers->set('x-vercel-oidc-token', 'runtime-oidc-token');

    Http::fake([
        'https://vercel.com/api/blob/?*' => Http::response([
            'url' => 'https://teststore.public.blob.vercel-storage.com/images/test.png',
        ]),
    ]);

    MediaStorage::store(
        UploadedFile::fake()->create('game.png', 10, 'image/png'),
        'images'
    );

    Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer runtime-oidc-token')
        && $request->hasHeader('x-vercel-blob-store-id', 'teststore'));
});
