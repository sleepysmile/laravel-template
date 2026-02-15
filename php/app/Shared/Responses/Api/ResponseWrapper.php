<?php

namespace App\Shared\Responses\Api;

use Illuminate\Http\Resources\Json\ResourceResponse;

class ResponseWrapper extends ResourceResponse
{

    protected function calculateStatus()
    {
        return $this->resource->httpStatusCode();
    }
}
