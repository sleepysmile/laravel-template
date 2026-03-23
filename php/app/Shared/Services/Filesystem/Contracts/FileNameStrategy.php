<?php

namespace App\Shared\Services\Filesystem\Contracts;

interface FileNameStrategy
{
    public function name(): string;
}
