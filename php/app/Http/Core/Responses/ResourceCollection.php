<?php

namespace App\Http\Core\Responses;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection as BaseResourceCollection;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

class ResourceCollection extends BaseResourceCollection
{
    public static $wrap = "results";

    public function __construct(
        Collection|Paginator $data,
        public readonly int $httpStatusCode = Response::HTTP_OK,
        public readonly bool $success = true,
    )
    {
        parent::__construct($data);
    }

    public function with(Request $request)
    {
        return [
            "success" => $this->success,
            "meta" => $this->meta()
        ];
    }

    protected function meta()
    {
        $meta = [];

        if ($this->resource instanceof Paginator) {
            $meta["per_page"] = $this->resource->perPage();
            $meta["page"] = $this->resource->currentPage();
        }

        return $meta;
    }

    public function httpStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    public function toResponse($request): JsonResponse
    {
        return (new ResponseWrapper($this))->toResponse($request);
    }
}
