<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\Access\AuthorizationException;
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
        // Handle general authentication exceptions for API requests
        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return new JsonResponse([
                    'message' => 'Unauthenticated.',
                    'error' => 'Token required or invalid',
                ], 401);
            }
        });

        // Handle Sanctum specific exceptions
        $this->renderable(function (\Laravel\Sanctum\Exceptions\MissingAbilityException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return new JsonResponse([
                    'message' => 'Forbidden.',
                    'error' => 'Insufficient permissions',
                ], 403);
            }
        });

        // Handle authorization exceptions for API requests
        $this->renderable(function (AuthorizationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return new JsonResponse([
                    'message' => 'Unauthorized.',
                    'error' => 'Not authorized to perform this action',
                ], 403);
            }
        });

        // Handle route not found exceptions that might occur during auth redirects
        $this->renderable(function (\Illuminate\Routing\Exceptions\RouteNotFoundException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                // Check if this is related to login redirect attempt
                $message = $e->getMessage();
                if (strpos($message, 'login') !== false) {
                    return new JsonResponse([
                        'message' => 'Unauthenticated.',
                        'error' => 'Token required or invalid',
                    ], 401);
                }
            }
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
