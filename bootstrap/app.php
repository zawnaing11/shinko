<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/health_check',
        then: function () {
            Route::middleware('web')
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));

            Route::middleware('web')
                ->prefix('company')
                ->name('company.')
                ->group(base_path('routes/company.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append([
            \App\Http\Middleware\TrustProxies::class,
            \App\Http\Middleware\LoggingInfoMiddleware::class,
        ]);
        $middleware->alias([
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'is_active' => \App\Http\Middleware\IsActive::class,
        ]);
        // 認証されていないユーザーのリダイレクト
        $middleware->redirectGuestsTo(function (Request $request) {
            if (! $request->expectsJson()) {
                if ($request->routeIs('admin.*')) {
                    return route('admin.login');
                } elseif ($request->routeIs('company.*')) {
                    return route('company.login');
                }
                return route('login');
            }
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // APIへのリクエストで例外が発生したとき、JSONで例外レスポンスを返すようにする
        $exceptions->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
/*
                if ($e instanceof AuthenticationException) {
                    return response()->json(['message' => 'Unauthorized.'], 401);
                } else if ($e instanceof NotFoundHttpException || $e instanceof ModelNotFoundException) {
                    return response()->json(['message' => 'Not Found.'], 404);
                } else if ($e instanceof HttpException) {
                    return response()->json(['message' => 'Server Error.'], 500);
                ] else if ($e instanceof ValidationException) {
                    return response()->json(['messages' => $e->validator->errors()->toArray()], 400);
                }
*/
                if ($e instanceof HttpException) {
                    $cast = fn ($orig): HttpException => $orig;  // HttpException へ型変換
                    $httpEx = $cast($e);

                    $message = null;
                    switch ($httpEx->getStatusCode()) {
                        case 401:
                            $message = __('Unauthorized.');
                            break;
                        case 403:
                            $message = __('Forbidden.');
                            break;
                        case 404:
                            $message = __('Not Found.');
                            break;
                        case 419:
                            $message = __('Page Expired.');
                            break;
                        case 429:
                            $message = __('Too Many Requests.');
                            break;
                        case 500:
                            $message = __('Server Error.');
                            break;
                        case 503:
                            $message = __('Service Unavailable.');
                            break;
                        default:
                            return;
                    }

                    return response()->json([
                        'message' => $message,
                    ], $httpEx->getStatusCode(), [
                        'Content-Type' => 'application/problem+json',
                    ]);
                }
            }
        });

    })->create();
