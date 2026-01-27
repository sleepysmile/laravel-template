<?php

namespace App\Http\Client\Handlers\Signin;

use Spatie\LaravelData\Data;

class SigninCommand extends Data
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
    )
    {}
}
