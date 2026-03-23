<?php

namespace App\Shared\Services\Filesystem;

use App\Shared\Services\Filesystem\Adapters\FileAdapter;
use App\Shared\Services\Filesystem\Adapters\FilesystemAdapter;
use App\Shared\Services\Filesystem\FileNameStrategies\UuidStrategy;
use App\Shared\Services\Filesystem\FilePathStrategies\ModelStrategy;
use Illuminate\Database\Eloquent\Model;

class ModelFilesystem
{
    protected FilesystemAdapter $filesystem;

    public function __construct(FilesystemAdapter $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    protected function savedPath(Model $model, string $attributeName, string $extension): string
    {
        $filePathStrategy = new ModelStrategy($model, $attributeName);
        $fileNameStrategy = new UuidStrategy();

        $directory = $filePathStrategy->path();
        $fileName = $fileNameStrategy->name();

        return "{$directory}{$fileName}.{$extension}";
    }

    public function delete(string $path): bool
    {
        if ($this->filesystem->exists($path)) {
            return $this->filesystem->delete($path);
        }

        return true;
    }

    public function store(Model $model, string $attributeName, FileAdapter $uploadedFile): string
    {
        $oldFilePath = $model->getAttribute($attributeName);

        if (null !== $oldFilePath) {
            $this->delete($oldFilePath);
        }

        $savedPath = $this->savedPath(
            $model,
            $attributeName,
            $uploadedFile->extension()
        );

        return $this->filesystem->put(
            $savedPath,
            $uploadedFile->content()
        );
    }
}
