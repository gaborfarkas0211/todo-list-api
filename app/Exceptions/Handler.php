<?php

namespace App\Exceptions;

use App\Traits\HasJsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use HasJsonResponse;

    private const HTTP_EXCEPTION_CODES = [
        NotFoundHttpException::class => Response::HTTP_NOT_FOUND,
        ModelNotFoundException::class => Response::HTTP_NOT_FOUND,
        MethodNotAllowedHttpException::class => Response::HTTP_METHOD_NOT_ALLOWED,
        BadRequestHttpException::class => Response::HTTP_BAD_REQUEST,
    ];
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
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
        foreach (self::HTTP_EXCEPTION_CODES as $exceptionClass => $code) {
            if ($e instanceof $exceptionClass) {
                return $this->sendError($e->getMessage(), $code);
            }
        }

        return $this->sendError($e->getMessage(), Response::HTTP_BAD_REQUEST);
    }
}
