<?php

namespace App\Http\Client\Queries\Message;

use Spatie\LaravelData\Data;

class FindMessageQueryCommand extends Data
{
    public function __construct(
        public readonly int $chat_id,
        public readonly int $page = 1,
        public readonly int $per_page = 20,
        public readonly string $order = 'asc',
    )
    {
    }
}

