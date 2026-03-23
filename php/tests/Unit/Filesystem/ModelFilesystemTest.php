<?php

namespace Tests\Unit\Filesystem;

use App\Shared\Services\Filesystem\Adapters\FileAdapter;
use App\Shared\Services\Filesystem\Adapters\FilesystemAdapter;
use App\Shared\Services\Filesystem\ModelFilesystem;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Depends;
use Tests\Factory\UserFactory;

class ModelFilesystemTest extends TestCase
{
    public function test_put()
    {
        $userFactory = new UserFactory();
        $storage = Storage::fake();

        $fsAdapter = new FilesystemAdapter($storage);
        $filesystem = new ModelFilesystem($fsAdapter);

        $model = $userFactory->user();
        $uploadFile = UploadedFile::fake()->image("test.jpg");

        $putSavePath = $filesystem->store(
            $model,
            "test",
            FileAdapter::fromUploadedFile($uploadFile)
        );

        $storage->assertExists($putSavePath);

        return [
            "model" => $model,
            "path" => $putSavePath
        ];
    }

    #[Depends("test_put")]
    public function test_store(array $bag)
    {
        $model = $bag["model"];
        $putSavePath = $bag["path"];

        $storage = Storage::fake();

        $fsAdapter = new FilesystemAdapter($storage);
        $filesystem = new ModelFilesystem($fsAdapter);

        $uploadFile = UploadedFile::fake()->image("test.jpg");

        $storeSavePath = $filesystem->store(
            $model,
            "test",
            FileAdapter::fromUploadedFile($uploadFile)
        );

        $storage->assertExists($storeSavePath);
        $storage->assertMissing($putSavePath);

        return [
            "path" => $storeSavePath
        ];
    }

    #[Depends("test_store")]
    public function test_delete(array $bag)
    {
        $storeSavePath = $bag["path"];

        $storage = Storage::fake();

        $fsAdapter = new FilesystemAdapter($storage);
        $filesystem = new ModelFilesystem($fsAdapter);

        $result = $filesystem->delete($storeSavePath);

        $this->assertTrue($result);
        $storage->assertMissing($storeSavePath);
    }
}
