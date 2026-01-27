<?php

namespace App\Http\Core\Responses;

class SuccessResponse extends BaseResponse
{
    public function __construct(mixed $data)
    {
        parent::__construct($data);
    }
}
