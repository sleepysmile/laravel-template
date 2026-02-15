<?php

namespace App\Shared\Services;

class TokenGenerator
{
    protected null|string $token = null;

    public function token()
    {
        if ($this->token === null) {
            $this->token = bin2hex(random_bytes(4));
        }

        return $this->token;
    }
}
