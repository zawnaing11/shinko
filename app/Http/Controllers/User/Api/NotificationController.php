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
        $notifications = Notification::active()->get();

        return response()->json(NotificationResource::collection($notifications));
    }

    public function show(Notification $notification)
    {
        if ($notification->is_active !== 1) {
            abort(404);
        }

        return response()->json($notification);
    }
}
