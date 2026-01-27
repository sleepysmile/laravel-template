<?php

namespace App\Http\Client\Ui\Responses;

use App\Http\Core\Responses\SuccessResponse;
use App\Models\Message;
use Illuminate\Http\Request;

/**
 * @property Message $resource
 */
class FindMessageResponse extends SuccessResponse
{
    public function __construct(Message $data)
    {
        parent::__construct($data);
    }

    public function toArray(Request $request)
    {
        return [
            "id" => $this->resource->id,
            "chat_id" => $this->resource->chat_id,
            "sender_id" => $this->resource->sender_id,
            "body" => $this->resource->body,
            "created_at" => optional($this->resource->created_at)?->toISOString(),
        ];
    }
}

