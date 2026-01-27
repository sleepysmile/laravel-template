<?php

namespace App\Http\Client\Queries\User;

use Spatie\LaravelData\Data;

class FindUserQueryCommand extends Data
{
    public function __construct(
        public readonly bool $withoutCurrentUser = false
    )
    {}
}
