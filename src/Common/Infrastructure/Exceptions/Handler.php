<?php

namespace Src\Common\Infrastructure\Exceptions;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response as MainResponse;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Src\Common\Infrastructure\Facades\Response;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register()
    {
        // $this->reportable(function (Throwable $e) {
        //     Integration::captureUnhandledException($e);
        // });
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof HttpException) {
            $statusCode = $e->getStatusCode();
            $message = MainResponse::$statusTexts[$statusCode];

            return Response::message($message)
                ->send($statusCode);
        }

        if ($e instanceof ModelNotFoundException) {
            $model = strtolower(class_basename($e->getModel()));
            return Response::message('global.errors.not_found')
                ->send(MainResponse::HTTP_NOT_FOUND);
        }

        if ($e instanceof BadRequestException) {
            return Response::message($e->getMessage())
                ->send(MainResponse::HTTP_BAD_REQUEST);
        }

        if ($e instanceof AuthorizationException) {
            return Response::message($e->getMessage())
                ->send(MainResponse::HTTP_FORBIDDEN);
        }

        if ($e instanceof UnauthorizedException) {
            return Response::message($e->getMessage())
                ->send(MainResponse::HTTP_FORBIDDEN);
        }

        if ($e instanceof AuthenticationException) {
            return Response::message($e->getMessage())
                ->send(MainResponse::HTTP_UNAUTHORIZED);
        }

        if ($e instanceof ValidationException) {
            $errors = $e->validator->errors()->messages();

            return Response::errors($errors)
                ->message($e->getMessage())
                ->send(MainResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($e instanceof ClientException) {
            $errors = $e->getResponse()->getBody();
            $code = $e->getCode();

            return Response::errors($errors)
                ->send($code);
        }

        if (env('APP_DEBUG', false)) {
            return parent::render($request, $e);
        }

        return Response::message('Unexpected Error , try later please')
            ->send(MainResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}
