<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Shared\Models\User;
use App\Shared\Services\Filesystem\Adapters\FileAdapter;
use App\Shared\Services\Filesystem\Adapters\FilesystemAdapter;
use App\Shared\Services\Filesystem\FileNameStrategies\UuidStrategy;
use App\Shared\Services\Filesystem\FilePathStrategies\ModelStrategy;
use App\Shared\Services\Filesystem\ModelFilesystem;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TestCommand extends Command
{
    protected $name = "x:test";

    public function handle()
    {
        $this->info("test");

        $model = new User();

        $file = UploadedFile::fake()->image("test.jpg");

        $fsAdapter = new FilesystemAdapter(Storage::disk());
        $mediator = new ModelFilesystem($fsAdapter);

        $model->avatar = $mediator->store($model, "avatar", FileAdapter::fromUploadedFile($file));
    }
}
