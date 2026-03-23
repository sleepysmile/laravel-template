<?php

namespace App\Shared\Services\Filesystem\Adapters;

use Illuminate\Contracts\Filesystem\Filesystem;

class FilesystemAdapter
{
    protected Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function exists(string $path): bool
    {
        return $this->filesystem->exists($path);
    }

    public function delete(string $path): bool
    {
        return $this->filesystem->delete($path);
    }

    public function put(string $savedPath, string $content): string
    {
        $this->filesystem->put($savedPath, $content, []);

        return $savedPath;
    }

    public function writeStream(string $filePath, string $contents)
    {

    }
}
