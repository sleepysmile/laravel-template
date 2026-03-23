<?php

namespace App\Shared\Services\Filesystem\FilePathStrategies;

class StaticExportStrategy extends StaticDirectoryStrategy
{
    protected function directory(): string
    {
        return "exports";
    }
}
