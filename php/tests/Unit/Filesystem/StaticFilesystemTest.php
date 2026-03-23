<?php

namespace Tests\Unit\Filesystem;

use App\Shared\Services\Filesystem\Adapters\FileAdapter;
use App\Shared\Services\Filesystem\Adapters\FilesystemAdapter;
use App\Shared\Services\Filesystem\StaticFilesystem;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Depends;

class StaticFilesystemTest extends TestCase
{
    public function test_put()
    {
        $storage = Storage::fake();

        $fsAdapter = new FilesystemAdapter($storage);
        $filesystem = new StaticFilesystem($fsAdapter);

        $uploadFile = UploadedFile::fake()->image("test.jpg");

        $putSavePath = $filesystem->store(
            FileAdapter::fromUploadedFile($uploadFile)
        );

        $storage->assertExists($putSavePath);

        return [
            "path" => $putSavePath
        ];
    }

    #[Depends("test_put")]
    public function test_store(array $bag)
    {
        $putSavePath = $bag["path"];

        $storage = Storage::fake();

        $fsAdapter = new FilesystemAdapter($storage);
        $filesystem = new StaticFilesystem($fsAdapter);

        $uploadFile = UploadedFile::fake()->image("test.jpg");

        $storeSavePath = $filesystem->store(
            FileAdapter::fromUploadedFile($uploadFile)
        );

        $storage->assertExists($storeSavePath);
        $storage->assertMissing($putSavePath);

        return [
            "path" => $putSavePath
        ];
    }

    #[Depends("test_store")]
    public function test_delete(array $bag)
    {
        $storeSavePath = $bag["path"];

        $storage = Storage::fake();

        $fsAdapter = new FilesystemAdapter($storage);
        $filesystem = new StaticFilesystem($fsAdapter);

        $result = $filesystem->delete($storeSavePath);

        $this->assertTrue($result);
        $storage->assertMissing($storeSavePath);
    }
}
