<?php

namespace App\Http\Controllers\User\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::active()
            ->paginate(config('const.default_paginate_number'));

        return response()->json(NotificationResource::collection($notifications)->response()->getData());
    }

    public function show(string $id)
    {
        $notification = Notification::where('id', $id)->active()->first();
        return response()->json(new NotificationResource($notification));
    }
}
