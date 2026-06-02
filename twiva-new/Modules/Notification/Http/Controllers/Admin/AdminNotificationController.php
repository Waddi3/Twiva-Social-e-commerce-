<?php

namespace Modules\Notification\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Modules\Notification\Entities\NotificationTitle;
use Modules\Notification\Http\Requests\Translation\UpdateTranslationRequest;
use Modules\System\Entities\System;

class AdminNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $notifications_list_titles = NotificationTitle::all();
//        $locale = app()->getLocale();

//        dd($notifications_list_titles);
        return view('notification::translations.manage', [
            'notifications_list_titles' => $notifications_list_titles,
        ]);
    }

    public function update(UpdateTranslationRequest $request)
    {
        $translations = $request->validated();
//        $path = module_path('Notification') . '/Resources/lang/en/notifications.php';
//        $output = "<?php\n\nreturn " . var_export($translations, true) . ';' . \PHP_EOL;
//        $file = new Filesystem();
//        $file->put($path, $output);
        $translations_to_update = [];
        foreach ($translations as $slug => $title) {
            $translations_to_update[] = [
                'slug' => $slug,
                'title' => $title,
            ];
        }
        NotificationTitle::upsert($translations_to_update, ['slug'], ['title']);
        return redirect()->route('admin.system.show')->with(['message' => 'Notifications Titles Update']);
    }

}
