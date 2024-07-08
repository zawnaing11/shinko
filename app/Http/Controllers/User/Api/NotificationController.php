<?php

namespace App\Http\Controllers\User\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::enabled()->get();

        return response()->json($notifications);
    }

    public function show(Notification $notification)
    {
        return response()->json($notification);
    }
}
