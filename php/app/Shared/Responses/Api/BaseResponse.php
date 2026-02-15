<?php

namespace App\Shared\Responses\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class BaseResponse extends JsonResource
{
    public static $wrap = "results";

    public function __construct(
        mixed $data,
        public readonly int $httpStatusCode = Response::HTTP_OK,
        public readonly bool $success = true,
    )
    {
        parent::__construct($data);
    }

    public function with(Request $request): array
    {
        return ["success" => $this->success];
    }

    public function httpStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    protected static function newCollection($resource)
    {
        return new ResourceCollection($resource);
    }

    public function toResponse($request): JsonResponse
    {
        return (new ResponseWrapper($this))->toResponse($request);
    }
}
