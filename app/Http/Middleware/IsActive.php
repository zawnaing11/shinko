<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Responses\Admin\LogoutResponse as AdminLogoutResponse;
use App\Responses\Company\LogoutResponse as CompanyLogoutResponse;

class IsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $guard)
    {
        if (Auth::guard($guard)->user()->is_active !== 1) {
            Auth::guard($guard)->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return ucfirst($guard) . LogoutResponse::class;
        }
        return $next($request);
    }
}
