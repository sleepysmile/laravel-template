<?php

namespace App\Http\Client\Handlers\Chat;

use Spatie\LaravelData\Data;

class CreateOrGetChatCommand extends Data
{
    public function __construct(
        public readonly int $user_id
    )
    {
    }
}
