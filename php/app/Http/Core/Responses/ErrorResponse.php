<?php

namespace App\Http\Core\Responses;

use Illuminate\Http\Request;

class ErrorResponse extends BaseResponse
{
    public static $wrap = "details";
    protected string $message;

    public function with(Request $request): array
    {
        $parent =  parent::with($request);
        return array_merge($parent, [
            "error" => $this->message
        ]);
    }

    public function __construct(string $message, int $httpStatusCode, string $token, string $file = null, int $line = null)
    {
        $data = [
            "token" => $token
        ];

        if ($file !== null && $line !== null && ! app()->isProduction()) {
            $data["file"] = "{$file}({$line})";
        }

        parent::__construct($data, $httpStatusCode, false);
        $this->message = $message;
    }
}
