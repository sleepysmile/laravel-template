<?php

namespace App\Http\Client\Queries\Message;

use Spatie\LaravelData\Data;

class MessagePoolingQueryCommand extends Data
{
    public function __construct(
        public readonly int $chat_id,
        public readonly ?int $last_id = null,
        public readonly int $timeout = 25,
        public readonly int $interval_ms = 500,
        public readonly string $order = 'asc',
    )
    {
    }
}

