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

            if ($e instanceof \Illuminate\Database\QueryException) {
                $errorCode = $e->errorInfo[1] ?? null;
                $friendlyMessage = 'A database validation error occurred.';
                $field = 'general';

                if ($errorCode == 1062) {
                    $friendlyMessage = 'A record with this information already exists.';
                    if (str_contains($e->getMessage(), 'admission_no')) {
                        $field = 'admission_no';
                        $friendlyMessage = 'The admission number has already been taken.';
                    } elseif (str_contains($e->getMessage(), 'email')) {
                        $field = 'email';
                        $friendlyMessage = 'The email address has already been taken.';
                    } elseif (str_contains($e->getMessage(), 'student_code')) {
                        $field = 'student_code';
                        $friendlyMessage = 'The student code has already been taken.';
                    }
                } elseif ($errorCode == 1048) {
                    if (preg_match("/Column '([^']+)' cannot be null/", $e->getMessage(), $matches)) {
                        $field = $matches[1];
                        $friendlyMessage = "The " . str_replace('_', ' ', $matches[1]) . " field cannot be null.";
                    } else {
                        $friendlyMessage = 'A required field cannot be null.';
                    }
                } elseif ($errorCode == 1451 || $errorCode == 1452) {
                    $friendlyMessage = 'The referenced record does not exist in the database.';
                }

                return response()->json([
                    'success' => false,
                    'message' => $friendlyMessage,
                    'errors'  => [
                        $field => [$friendlyMessage]
                    ]
                ], 422);
            }

            $statusCode = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;
            $errorMessage = (config('app.debug') || $e instanceof HttpExceptionInterface)
                ? ($e->getMessage() ?: 'An unexpected server error occurred.')
                : 'An unexpected server error occurred.';

            $response = [
                'success' => false,
                'message' => $errorMessage,
                'error'   => $errorMessage,
            ];

            if (config('app.debug')) {
                $response['exception'] = get_class($e);
                $response['file'] = $e->getFile();
                $response['line'] = $e->getLine();
            }

            return response()->json($response, $statusCode);
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

