<?php

use App\Http\Middleware\DetectPlatform;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        $middleware->append(DetectPlatform::class);
        $middleware->web(append: [
            \App\Http\Middleware\AuthTokenBridge::class,
        ]);
        $middleware->alias([
            'platform' => DetectPlatform::class,
            'profile.complete' => \App\Http\Middleware\EnsureProfileIsComplete::class,
        ]);

        // Trust the NativePHP Jump proxy so sessions work correctly
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR |
                     Request::HEADER_X_FORWARDED_HOST |
                     Request::HEADER_X_FORWARDED_PORT |
                     Request::HEADER_X_FORWARDED_PROTO
        );

        // Replace default ValidatePostSize with SafeValidatePostSize to handle proxy header duplication
        $middleware->replace(
            \Illuminate\Http\Middleware\ValidatePostSize::class,
            \App\Http\Middleware\SafeValidatePostSize::class
        );

        // Exclude authentication and native mobile bridge routes from CSRF mismatch errors
        $middleware->validateCsrfTokens(except: [
            'login',
            'register',
            'complete-profile',
            'logout',
            'forgot-password',
            'reset-password',
            '_native/*',
            'api/*',
            'webhooks/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Session expired. Please refresh and try again.',
                ], 419);
            }

            return redirect()
                ->back()
                ->withInput($request->except('_token', 'password', 'password_confirmation'))
                ->with('error', 'Your session expired. Please try again.');
        });
    })->create();
