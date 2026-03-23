<?php

namespace App\Api\Handlers\User;

use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Data;

class UpdateUserCommand extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly UploadedFile $avatar
    )
    {
    }
}
