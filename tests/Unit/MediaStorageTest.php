<?php

use App\Support\MediaStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(TestCase::class);

test('uploaded media is stored and deleted through the configured disk', function () {
    Storage::fake('uploads');
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
