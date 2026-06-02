<?php

namespace Modules\Admin\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Notification\Entities\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::with('user')->whereHas('user')->latest()->paginate(10);
        return view('admin::notifications.index', compact('notifications'));
    }
}
