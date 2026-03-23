<?php

namespace Tests\Unit\Filesystem;

use App\Shared\Services\Filesystem\Adapters\FilesystemAdapter;
use App\Shared\Services\Filesystem\ResourceFilesystem;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Depends;

class ResourceFilesystemTest extends TestCase
{
    public function test_put()
    {
        $storage = Storage::fake();

        $fsAdapter = new FilesystemAdapter($storage);
        $filesystem = new ResourceFilesystem($fsAdapter);

        $uploadFile = UploadedFile::fake()->image("test.jpg");
    }

    #[Depends("test_put")]
    public function test_store(array $bag)
    {

    }

    #[Depends("test_store")]
    public function test_delete(array $bag)
    {

    }
}
