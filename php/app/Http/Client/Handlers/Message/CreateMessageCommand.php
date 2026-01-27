<?php

namespace App\Http\Client\Handlers\Message;

use Spatie\LaravelData\Data;

class CreateMessageCommand extends Data
{
    public function __construct(
        public readonly int $chat_id,
        public readonly string $body,
    )
    {
    }
}

