<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NotificationRequest;
use App\Models\Notification;
use App\Traits\UploadTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class NotificationController extends Controller
{
    use UploadTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $notifications = Notification::orderBy('updated_at', 'DESC');
        if ($request->filled('title')) {
            $notifications->where('title', 'like', '%' . $request->title . '%');
        }
        if ($request->filled('is_active')) {
            $notifications->where('is_active', $request->is_active);
        }
        if ($request->filled('publish_from')) {
            $notifications->where('publish_date', '>=', $request->publish_from);
        }
        if ($request->filled('publish_to')) {
            $notifications->where('publish_date', '<=', $request->publish_to);
        }
        $notifications = $notifications->paginate(config('const.default_paginate_number'));

        return view('admin.notifications.index', ['notifications' => $notifications]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.notifications.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NotificationRequest $request)
    {
        $validated = $request->validated();

        try {
            $validated['image'] = $this->imageUpload($validated['is_image'], config('const.notification_image_path'), config('const.notification_tmp_path'));

            Notification::create($validated);

        } catch (Exception $e) {
            logger()->error('$e', [$e->getCode(), $e->getMessage()]);
            $new_file_path = config('const.notification_image_path') . $validated['image'];
            if (! empty($new_file_path) && ! empty($validated['is_image'])) {
                Storage::move($new_file_path, config('const.notification_tmp_path') . $validated['is_image']);
            }

            return back()
                ->with('alert.error', 'お知らせ作成に失敗しました。')
                ->withInput();
        }

        return redirect()
            ->route('admin.notifications.index')
            ->with('alert.success', 'お知らせを作成しました。');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notification $notification)
    {
        return view('admin.notifications.edit', ['notification' => $notification]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NotificationRequest $request, Notification $notification)
    {
        $validated = $request->validated();

        try {
            $validated['image'] = $this->imageUpload($validated['is_image'], config('const.notification_image_path'), config('const.notification_tmp_path'), $notification->image);

            $notification->fill($validated)->save();

        } catch (Exception $e) {
            logger()->error('$e', [$e->getCode(), $e->getMessage()]);
            $new_file_path = config('const.notification_image_path') . $validated['image'];
            if (! empty($new_file_path) && ! empty($validated['is_image'])) {
                Storage::move($new_file_path, config('const.notification_tmp_path') . $validated['is_image']);
            }

            return back()
                ->with('alert.error', 'お知らせ更新に失敗しました。')
                ->withInput();
        }

        return redirect()
            ->route('admin.notifications.index')
            ->with('alert.success', 'お知らせを更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notification)
    {
        try {
            DB::transaction(function() use($notification) {
                $notification->delete();
                Storage::delete(config('const.notification_image_path') . $notification->image);
            });

        } catch (Exception $e) {
            logger()->error('$e', [$e->getCode(), $e->getMessage()]);

            return back()
                ->with('alert.error', 'お知らせ削除に失敗しました。')
                ->withInput();
        }

        return redirect()
            ->route('admin.notifications.index')
            ->with('alert.success', 'お知らせを削除しました。');
    }
}
