<?php

namespace App\Exceptions;

use App\Exceptions\Api\BadRequestException as ApiBadRequestException;
use App\Exceptions\Api\CustomException as ApiCustomException;
use App\Exceptions\Api\ErrorException as ApiErrorException;
use App\Exceptions\Api\InternalErrorException as ApiInternalErrorException;
use App\Exceptions\Api\NotFoundException as ApiNotFoundException;
use App\Exceptions\Api\UnauthorizedException as ApiUnauthorizedException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($this->shouldReturnJson($request, $e)) {
            $err = match (true) {
                $e instanceof ApiCustomException => $e,
                $e instanceof BadRequestHttpException => new ApiBadRequestException($e->getMessage()),
                $e instanceof NotFoundHttpException => new ApiNotFoundException($e->getMessage()),
                $e instanceof ModelNotFoundException => new ApiNotFoundException($e->getMessage()),
                $e instanceof ValidationException => (new ApiBadRequestException($e->getMessage()))->setErrors($e->errors()),
                $e instanceof AuthenticationException => new ApiUnauthorizedException($e->getMessage()),
                $e instanceof UnauthorizedHttpException => new ApiUnauthorizedException($e->getMessage()),
                $e instanceof HttpException => new ApiErrorException($e->getMessage(), $e->getStatusCode()),
                default => App::environment('production') ? new ApiInternalErrorException($e->getMessage()) : $e,
            };
        }

        return parent::render($request, $err);
    }
}
