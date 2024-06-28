<?php

namespace App\Http\Middleware;

use Closure;

class LoggingInfoMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->path() != 'health_check') {
            $info =[
                'fullUrl' => $request->fullUrl(),
                'ip' => $request->ip(),
                'ua' => $request->headers->get('user-agent'),
            ];
            logger()->info('$info', $info);
        }
        return $next($request);
    }
}
