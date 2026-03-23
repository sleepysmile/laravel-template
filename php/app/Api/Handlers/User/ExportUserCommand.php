<?php

namespace App\Api\Handlers\User;

use Spatie\LaravelData\Data;

class ExportUserCommand extends Data
{
    public function __construct(
        public readonly array $fields = ["*"]
    )
    {
    }
}
