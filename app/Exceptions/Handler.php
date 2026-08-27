<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
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
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            if ($e instanceof ValidationException) {
                return parent::render($request, $e);
            }

            if ($e instanceof AuthenticationException) {
                return $this->unauthenticated($request, $e);
            }

            $statusCode = 500;
            if ($e instanceof HttpExceptionInterface) {
                $statusCode = $e->getStatusCode();
            } else {
                $code = $e->getCode();
                if (is_numeric($code) && (int)$code >= 400 && (int)$code < 600) {
                    $statusCode = (int)$code;
                }
            }

            if ($statusCode < 100 || $statusCode > 599) {
                $statusCode = 500;
            }

            $errorMessage = $e->getMessage() ?: 'An unexpected server error occurred.';

            return response()->json([
                'success'   => false,
                'message'   => $errorMessage,
                'error'     => $errorMessage,
                'exception' => get_class($e),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
            ], $statusCode);
        }

        return parent::render($request, $e);
    }

    /**
     * Handle an unauthenticated user.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json([
            'success' => false,
            'message' => 'No token provided'
        ], 401);
    }
}

