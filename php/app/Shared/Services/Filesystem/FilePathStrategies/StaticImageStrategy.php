<?php

namespace App\Shared\Services\Filesystem\FilePathStrategies;

class StaticImageStrategy extends StaticDirectoryStrategy
{
    protected function directory(): string
    {
        return "images";
    }
}
