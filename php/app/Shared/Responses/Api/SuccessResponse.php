<?php

namespace App\Shared\Responses\Api;

class SuccessResponse extends BaseResponse
{
    public function __construct(mixed $data)
    {
        parent::__construct($data);
    }
}
