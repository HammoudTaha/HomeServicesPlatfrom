<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Exceptions\BaseApiException;
use App\Traits\ApiResponse;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(App\Http\Middleware\ForceJsonResponse::class);
        $middleware->prepend(
            App\Http\Middleware\EnsureActiveUser::class,
        );
        $middleware->throttleApi('normal');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            return $request->is('api/*') || $request->expectsJson();
        });
        $exceptions->render(function (BaseApiException $e, Request $request) {
            return ApiResponse::error($e->getMessage(), (int) $e->statusCode);
        });
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::error(
                'The provided data is not valid. Please enter valid data and retry again.',
                400,
                $e->errors()
            );
        });
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e) {
            return ApiResponse::error('You have exceeded the number of attempts allowed. Please try again later.', 429);
        });
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e) {
            return ApiResponse::error('You are not authenticated to perform this action.', 401);
        });
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e) {
            return ApiResponse::error('You are not authorized to perform this action.', 403);
        });
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            return ApiResponse::error('The requested resource was not found.', 404);
        });
        $exceptions->render(function (Throwable $e) {
            return ApiResponse::error('Something went wrong. Please try again later.', 500, $e->getMessage());
        });
    })->create();
