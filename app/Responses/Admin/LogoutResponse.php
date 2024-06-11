<?php

namespace App\Responses\Admin;

use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;

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
            : redirect()->intended('admin/login');
    }
}
