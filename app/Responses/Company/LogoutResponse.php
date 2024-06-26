<?php

namespace App\Responses\Company;

use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Laravel\Fortify\Fortify;

class LogoutResponse implements LogoutResponseContract
{
    /**
    * Create an HTTP response that represents the object.
    *
    * @param Request $request
    *
    * @return Response
    */
    public function toResponse($request)
    {
        return $request->wantsJson()
            ? new JsonResponse('', 204)
            : redirect()->intended(Fortify::redirects('logout.company'));
    }
}
