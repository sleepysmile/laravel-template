<?php

namespace App\Shared\Services\Filesystem;

use App\Shared\Services\Filesystem\Adapters\FileAdapter;
use App\Shared\Services\Filesystem\Adapters\FilesystemAdapter;
use App\Shared\Services\Filesystem\Contracts\FileNameStrategy;
use App\Shared\Services\Filesystem\Contracts\FilePathStrategy;
use App\Shared\Services\Filesystem\FileNameStrategies\UuidStrategy;
use App\Shared\Services\Filesystem\FilePathStrategies\StaticImageStrategy;

class StaticFilesystem
{
    protected FilesystemAdapter $filesystem;
    protected FilePathStrategy $pathStrategy;
    protected FileNameStrategy $nameStrategy;

    public function __construct(
        FilesystemAdapter $filesystem,
        FilePathStrategy|null $pathStrategy = null,
        FileNameStrategy|null $nameStrategy = null
    )
    {
        $this->filesystem = $filesystem;
        $this->pathStrategy = $pathStrategy ?: new StaticImageStrategy();
        $this->nameStrategy = $nameStrategy ?: new UuidStrategy();
    }

    public function setPathStrategy(FilePathStrategy $pathStrategy)
    {
        $this->pathStrategy = $pathStrategy;

        return $this;
    }

    public function setNameStrategy(FileNameStrategy $nameStrategy)
    {
        $this->nameStrategy = $nameStrategy;

        return $this;
    }

    public function delete(string $path)
    {
        if ($this->filesystem->exists($path)) {
            return $this->filesystem->delete($path);
        }

        return true;
    }

    public function store(FileAdapter $uploadedFile, string|null $oldFilePath = null): string
    {
        $directory = $this->pathStrategy->path();
        $fileName = $this->nameStrategy->name();
        $savedPath = "{$directory}{$fileName}.{$uploadedFile->extension()}";

        if (null !== $oldFilePath) {
            $this->delete($oldFilePath);
        }

        return $this->filesystem->put(
            $savedPath,
            $uploadedFile->content()
        );
    }
}
