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

        // APIのエラーレスポンス修正
        $exceptions->render(function (Exception $e, Request $request) {

            if ($request->is('api/*')) {

                if ($e instanceof AuthenticationException) {
                    return response()->json(['message' => 'Unauthorized.'], 401);
                } else if ($e instanceof NotFoundHttpException || $e instanceof ModelNotFoundException) {
                    return response()->json(['message' => 'Not Found.'], 404);
                } else if ($e instanceof HttpException) {
                    return response()->json(['message' => 'Server Error.'], 500);
                }

/* 以下は不要だが将来的に利用する可能性があるためコメントアウト 401発生時に型変換でエラーが発生する
                else if ($e instanceof ValidationException) {
                    // バリデーション
                    return response()->json(['messages' => $e->validator->errors()->toArray()], 400);
                }  else {
                    if ($e instanceof Exception) {
                        $cast = fn ($orig): HttpException => $orig; // HttpException へ型変換
                        $httpEx = $cast($e);

                        $message = null;
                        switch ($httpEx->getStatusCode()) {
                            case 401:
                                // $title = __('Unauthorized');
                                // $detail =  __('Unauthorized');
                                $message = __('Unauthorized.');
                                break;
                            case 403:
                                // $title = __('Forbidden');
                                // $detail = __($httpEx->getMessage() ?: 'Forbidden');
                                $message = __('Forbidden.');
                                break;
                            case 404:
                                // $title = __('Not Found');
                                // $detail = __('Not Found');
                                $message = __('Not Found.');
                                break;
                            case 419:
                                // $title = __('Page Expired');
                                // $detail = __('Page Expired');
                                $message = __('Page Expired.');
                                break;
                            case 429:
                                // $title = __('Too Many Requests');
                                // $detail = __('Too Many Requests');
                                $message = __('Too Many Requests.');
                                break;
                            case 500:
                                // $title = __('Server Error');
                                // $detail = config('app.debug') ? $httpEx->getMessage() : __('Server Error');
                                $message = __('Server Error.');
                                break;
                            case 503:
                                // $title = __('Service Unavailable');
                                // $detail = __('Service Unavailable');
                                $message = __('Service Unavailable.');
                                break;
                            default:
                                return;
                        }

                        return response()->json([
                            // 'title' => $title,
                            // 'status' => $httpEx->getStatusCode(),
                            // 'detail' => $detail,
                            'message' => $message,
                        ], $httpEx->getStatusCode(), [
                            'Content-Type' => 'application/problem+json',
                        ]);
                    }
                }
*/
            }

        });

    })->create();
