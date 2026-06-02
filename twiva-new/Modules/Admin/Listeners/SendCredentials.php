<?php

namespace Modules\Admin\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendCredentials implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
//        sleep(5);

        Mail::send('admin::emails.credentials', ['user_email' => $event->user->email, 'password' => $event->password ],
        function ($message) use ($event){
            $message->from(config('mail.from.address'), 'Twiva');
            $message->to($event->user->email)->subject('Twiva Credentials');
        });
    }
}
