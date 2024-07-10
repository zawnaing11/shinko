<?php

namespace App\Http\Controllers\Company;

use App\Actions\Company\AttemptToAuthenticate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Company\LoginRequest;
use App\Responses\Company\LoginResponse;
use App\Responses\Company\LogoutResponse;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Routing\Pipeline;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;

class AuthController extends Controller
{
    /**
    * The guard implementation.
    *
    * @var \Illuminate\Contracts\Auth\StatefulGuard
    */
    protected $guard;

    /**
    * Create a new controller instance.
    *
    * @param  \Illuminate\Contracts\Auth\StatefulGuard
    * @return void
    */
    public function __construct(StatefulGuard $guard)
    {
        $this->guard = $guard;
    }

    /**
    * Show the login view.
    *
    * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    */
    public function login()
    {
        return view('company.auth.login');
    }

    /**
    * Attempt to authenticate a new session.
    *
    * @param  \App\Http\Requests\Company\LoginRequest  $request
    * @return mixed
    */
    public function authenticate(LoginRequest $request)
    {
        $result = app(Pipeline::class)
            ->send($request)
            ->through([
                AttemptToAuthenticate::class,
                PrepareAuthenticatedSession::class
            ])
            ->then(function ($request) {
                return app(LoginResponse::class);
            });
        return $result;
    }

    /**
    * Destroy an authenticated session.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return App\Responses\Company\LogoutResponse
    */
    public function logout(Request $request): LogoutResponse
    {
        $this->guard->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return app(LogoutResponse::class);
    }

}