<?php

namespace App\Http\Core\Exceptions;

use App\Http\Core\Responses\ErrorResponse;
use App\Http\Core\Responses\ValidationErrorResponse;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ApiHandler extends ExceptionHandler
{
    protected TokenGenerator $tokenGenerator;

    public function __construct(Container $container, TokenGenerator $tokenGenerator)
    {
        parent::__construct($container);
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Throwable $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof ValidationException) {
            return (new ValidationErrorResponse($e->errors()))->toResponse($request);
        }

        $statusCode = HttpResponse::HTTP_INTERNAL_SERVER_ERROR;

        if ($e instanceof AuthenticationException) {
            $statusCode = HttpResponse::HTTP_UNAUTHORIZED;
        }

        if ($e instanceof AuthorizationException) {
            $statusCode = HttpResponse::HTTP_FORBIDDEN;
        }

        if ($e instanceof HttpExceptionInterface) {
            $statusCode = $e->getStatusCode();
        }

        return (new ErrorResponse(
            message: $e->getMessage(),
            httpStatusCode: $statusCode,
            token: $this->tokenGenerator->token(),
            file: $e->getFile(),
            line: $e->getLine()
        ))->toResponse($request);
    }

    /**
     * Report or log an exception.
     *
     * @param Throwable $exception
     * @return void
     *
     * @throws Exception
     */
    public function report(Throwable $e)
    {
    }
}
