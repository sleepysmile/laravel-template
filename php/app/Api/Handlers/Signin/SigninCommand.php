<?php

namespace App\Api\Handlers\Signin;

use Spatie\LaravelData\Data;

class SigninCommand extends Data
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
    )
    {}
}
