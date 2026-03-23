<?php

namespace App\Shared\Services\Filesystem\FilePathStrategies;

use App\Shared\Services\Filesystem\Contracts\FilePathStrategy;

abstract class StaticDirectoryStrategy implements FilePathStrategy
{
    abstract protected function directory(): string;

    public function path(): string
    {
        return "static"
            . DIRECTORY_SEPARATOR
            . $this->directory()
            . DIRECTORY_SEPARATOR
            . date("Y_m_d")
            . DIRECTORY_SEPARATOR;
    }
}
