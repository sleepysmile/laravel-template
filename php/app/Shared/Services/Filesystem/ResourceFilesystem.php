<?php

namespace App\Shared\Services\Filesystem;

use App\Shared\Services\Filesystem\Adapters\FilesystemAdapter;
use App\Shared\Services\Filesystem\Contracts\FileNameStrategy;
use App\Shared\Services\Filesystem\Contracts\FilePathStrategy;
use App\Shared\Services\Filesystem\FileNameStrategies\UuidStrategy;
use App\Shared\Services\Filesystem\FilePathStrategies\StaticImageStrategy;
use LogicException;

class ResourceFilesystem
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

    public function storeStream(string $extension, string $content, string $mode = "rw+")
    {
        $filename = "{$this->pathStrategy}{$this->nameStrategy}.{$extension}";

        $fileResource = fopen($filename, $mode);

        throw new LogicException("not implement");
    }

    public function open(string $extension, string $mode = "rw+")
    {
        $filename = "{$this->pathStrategy}{$this->nameStrategy}.{$extension}";

        return fopen($filename, $mode);
    }

    public function put($resource, string|array $contents): bool
    {
        throw new LogicException("not implement");
    }

    public function close($resource): string
    {
        throw new LogicException("not implement");
    }
}
