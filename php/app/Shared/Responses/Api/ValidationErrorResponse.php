<?php

namespace App\Shared\Responses\Api;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidationErrorResponse extends BaseResponse
{
    public static $wrap = "details";

    public function with(Request $request): array
    {
        $parent = parent::with($request);
        return array_merge($parent, [
            "error" => "Validation errors"
        ]);
    }

    public function __construct(array $errors)
    {
        parent::__construct($errors, Response::HTTP_BAD_REQUEST, false);
    }
}
