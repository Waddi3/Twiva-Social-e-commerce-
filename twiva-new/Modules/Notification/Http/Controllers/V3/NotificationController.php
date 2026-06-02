<?php


namespace Modules\Notification\Http\Controllers\V3;


use Modules\Notification\Entities\Notification;
use Modules\Notification\Http\Resources\NotificationCollection;
use Modules\User\Entities\User;

class NotificationController
{
    public function index()
    {
        return response()->json(
            ['data' => NotificationCollection::collection(auth()->user()->notifications()->paginate(10))->resource
            ]);
    }

    public function markNotificationAsRead()
    {
        Notification::findOrFail(request('notification_id'))->update(['read_at' => now()]);
        return response()->json(['message' => 'Notification Has Been marked as read']);
    }


    public function markAllNotificationsAsRead()
    {
        $userId = auth('api')->id();
        User::whereId($userId)->with('notifications', function ($notification) {
            $notification->whereNull('read_at');
        })->first()->notifications()->update(['read_at' => now()]);
        return response()->json(['message' => 'All Notifications Has Been marked as read']);
    }

}