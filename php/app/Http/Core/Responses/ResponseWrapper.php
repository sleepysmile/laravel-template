<?php

namespace App\Http\Core\Responses;

use Illuminate\Http\Resources\Json\ResourceResponse;

class ResponseWrapper extends ResourceResponse
{

    protected function calculateStatus()
    {
        return $this->resource->httpStatusCode();
    }
}
