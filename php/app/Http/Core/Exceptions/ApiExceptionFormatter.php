<?php

namespace App\Http\Core\Exceptions;

use App\Http\Core\Responses\ErrorResponse;
use App\Http\Core\Responses\ValidationErrorResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ApiExceptionFormatter
{
    public function __construct(
        protected Exceptions $exceptions,
        protected TokenGenerator $tokenGenerator
    )
    {
    }

    public function configure()
    {
        $this->exceptions->shouldRenderJsonWhen(function ($request, $e) {
            return $request->is('api/*') || $request->expectsJson();
        });

        $isApi = request()->is("api/*");

        if ($isApi) {
            $token = $this->tokenGenerator->token();
            $this->exceptions->render(function (ValidationException $exception, $request) {
                return (new ValidationErrorResponse($exception->errors()))->toResponse($request);
            });

            $this->exceptions->render(function (AuthenticationException $exception, $request) use ($token) {
                return (new ErrorResponse(
                    message: "Unauthorized",
                    httpStatusCode: HttpResponse::HTTP_UNAUTHORIZED,
                    token: $token
                ))
                    ->toResponse($request);
            });

            $this->exceptions->render(function (AuthorizationException $exception, $request) use ($token) {
                return (new ErrorResponse(
                    message: "Forbidden",
                    httpStatusCode: HttpResponse::HTTP_FORBIDDEN,
                    token: $token
                ))
                    ->toResponse($request);
            });

            $this->exceptions->render(function (HttpExceptionInterface $exception, $request) use ($token) {
                return (new ErrorResponse(
                    message: $exception->getMessage(),
                    httpStatusCode: $exception->getStatusCode(),
                    token: $token,
                    file: $exception->getFile(),
                    line: $exception->getLine()
                ))->toResponse($request);
            });

            $this->exceptions->render(function (Throwable $exception, $request) use ($token) {
                return (new ErrorResponse(
                    message: $exception->getMessage(),
                    httpStatusCode: HttpResponse::HTTP_INTERNAL_SERVER_ERROR,
                    token: $token,
                    file: $exception->getFile(),
                    line: $exception->getLine()
                ))->toResponse($request);
            });
        }
    }
}
