<?php

namespace App\Http\Controllers\User\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\NotificationResource;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::active()
            ->paginate(config('const.default_paginate_number'));

        return response()->json(NotificationResource::collection($notifications)->response()->getData());
    }

    public function show(Notification $notification)
    {
        $now = Carbon::now();
        if ($notification->is_active !== 1 || $notification->publish_date <= $now) {
            abort(404);
        }

        return response()->json(new NotificationResource($notification));
    }
}
