<?php

namespace App\Shared\Services\Filesystem\FileNameStrategies;

use App\Shared\Services\Filesystem\Contracts\FileNameStrategy;
use Illuminate\Support\Str;

class UuidStrategy implements FileNameStrategy
{
    public function name(): string
    {
        return Str::uuid()->toString();
    }
}
