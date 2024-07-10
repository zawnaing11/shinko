<?php

namespace App\Http\Controllers\User\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Api\UserRequest;
use App\Models\User;
use Exception;

class UserController extends Controller
{
    public function update(UserRequest $request, User $user)
    {
        try {
            $user->fill($request->validated())->save();

        } catch (Exception $e) {
            logger()->error('$e', [$e->getCode(), $e->getMessage()]);
            abort($e->getCode(), $e->getMessage());
        }

        return response()->json($user);
    }

}
