<?php

namespace App\Shared\Services\Filesystem\Contracts;

interface FilePathStrategy
{
    public function path(): string;
}
