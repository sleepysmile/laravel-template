<?php

namespace App\Console\Handlers;

use Spatie\LaravelData\Data;

class CreateUserCommand extends Data
{
    public function __construct(
        public string $email,
        public string $name,
        public string $password,
    )
    {}
}
