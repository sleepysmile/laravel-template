<?php

namespace App\Http\Client\Ui\Responses;

use App\Http\Core\Responses\SuccessResponse;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @property User $resource
 */
class FindUserResponse extends SuccessResponse
{
    public function __construct(User $data)
    {
        parent::__construct($data);
    }

    public function toArray(Request $request)
    {
        return [
            "id" => $this->resource->id,
            "name" => $this->resource->name,
            "email" => $this->resource->email,
        ];
    }
}
